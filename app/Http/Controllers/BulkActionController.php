<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BulkActionController extends Controller
{
    /**
     * Unified handler for all bulk actions.
     */
    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'action' => 'required|string',
            'type'   => 'required|string|in:users,providers,bookings,services',
        ]);

        $ids = $request->ids;
        $action = $request->action;
        $type = $request->type;

        try {
            return DB::transaction(function () use ($ids, $action, $type) {
                switch ($type) {
                    case 'users':
                        return $this->handleUsers($ids, $action);
                    case 'providers':
                        return $this->handleProviders($ids, $action);
                    case 'bookings':
                        return $this->handleAppointments($ids, $action);
                    case 'services':
                        return $this->handleServices($ids, $action);
                    default:
                        return response()->json(['success' => false, 'message' => 'Invalid type.'], 400);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function handleUsers(array $ids, string $action)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        if ($action === 'delete') {
            User::whereIn('id', $ids)->where('id', '!=', Auth::id())->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' users deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Action not supported for users.'], 400);
    }

    private function handleProviders(array $ids, string $action)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $providers = Provider::whereIn('user_id', $ids)->get();

        if ($action === 'approve') {
            foreach ($providers as $p) {
                DB::transaction(function () use ($p) {
                    $p->update(['status' => 'approved']);
                    $p->user->update(['status' => 'active']);
                    // Notify user via app logic
                    $p->user->notify(new \App\Notifications\ProviderStatusNotification('approved'));
                });
            }
            return response()->json(['success' => true, 'message' => $providers->count() . ' providers approved and notified.']);
        }

        if ($action === 'reject') {
            foreach ($providers as $p) {
                DB::transaction(function () use ($p) {
                    $p->update(['status' => 'rejected']);
                    $p->user->update(['status' => 'rejected']);
                    // Notify user via app logic
                    $p->user->notify(new \App\Notifications\ProviderStatusNotification('rejected'));
                });
            }
            return response()->json(['success' => true, 'message' => $providers->count() . ' providers rejected and notified.']);
        }

        return response()->json(['success' => false, 'message' => 'Action not supported for providers.'], 400);
    }

    private function handleAppointments(array $ids, string $action)
    {
        $user = Auth::user();
        $query = Appointment::whereIn('id', $ids);

        if ($user->isProvider()) {
            $query->where('provider_id', $user->providerProfile->id);
            if ($action === 'complete') {
                $query->update(['status' => Appointment::STATUS_COMPLETED]);
                return response()->json(['success' => true, 'message' => count($ids) . ' appointments completed.']);
            }
            if ($action === 'cancel') {
                $query->update(['status' => Appointment::STATUS_CANCELLED]);
                return response()->json(['success' => true, 'message' => count($ids) . ' appointments cancelled.']);
            }
        }

        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
            if ($action === 'cancel') {
                $query->update(['status' => Appointment::STATUS_CANCELLED]);
                return response()->json(['success' => true, 'message' => count($ids) . ' appointments cancelled.']);
            }
        }

        if ($user->isAdmin()) {
            if ($action === 'delete') {
                $query->delete();
                return response()->json(['success' => true, 'message' => count($ids) . ' appointments deleted.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized or invalid action.'], 403);
    }

    private function handleServices(array $ids, string $action)
    {
        if (!Auth::user()->isProvider()) abort(403);

        $query = Service::whereIn('id', $ids)->where('provider_id', Auth::user()->providerProfile->id);

        if ($action === 'activate') {
            $query->update(['status' => 1]); // Boolean true
            return response()->json(['success' => true, 'message' => count($ids) . ' services activated.']);
        }

        if ($action === 'deactivate') {
            $query->update(['status' => 0]); // Boolean false
            return response()->json(['success' => true, 'message' => count($ids) . ' services deactivated.']);
        }

        if ($action === 'delete') {
            $query->delete();
            return response()->json(['success' => true, 'message' => count($ids) . ' services deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Action not supported for services.'], 400);
    }
}
