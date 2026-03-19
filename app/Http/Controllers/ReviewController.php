<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\ReviewReceived;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'  => 'required|exists:bookings,id',
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Security & Business Rules
        // 1. Must belong to the user
        if ($booking->customer_id !== auth()->id()) {
            return back()->with('error', 'You can only review your own bookings.');
        }

        // 2. Must be completed
        if ($booking->status !== Booking::STATUS_COMPLETED) {
            return back()->with('error', 'You can only review completed appointments.');
        }

        // 3. Prevent duplicate reviews
        if ($booking->review()->exists()) {
            return back()->with('error', 'You have already reviewed this appointment.');
        }

        try {
            DB::beginTransaction();

            $review = Review::create([
                'booking_id'  => $booking->id,
                'customer_id' => auth()->id(),
                'provider_id' => $booking->provider_id,
                'rating'      => $request->rating,
                'review_text' => $request->review_text,
            ]);

            // Recalculate and cache provider rating
            $booking->provider->recalculateRating();

            // Notify provider
            $booking->provider->user->notify(new ReviewReceived($review));

            DB::commit();

            return back()->with('success', 'Thank you for your review!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit review. Please try again.');
        }
    }
}
