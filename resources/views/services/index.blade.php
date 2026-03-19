@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="min-height: calc(100vh - 70px); margin-top: -24px; background-color: var(--bg-body);">
    <div class="p-4 p-md-5">
        
        {{-- Header Section --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Service Catalog</h2>
                <p class="text-muted font-medium">Manage and optimize your professional service offerings</p>
            </div>
            {{-- Updated Button to Match your Screenshot --}}
            <a href="{{ route('provider.services.create') }}" class="btn-modern-primary">
                <i class="ph ph-plus-circle"></i> 
                <span>Create Service</span>
            </a>
        </div>

        {{-- Professional Alert --}}
        @if(session('success'))
            <div class="alert alert-modern-success border-0 mb-4 animate-fade-in shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="ph ph-check-circle mr-2" style="font-size: 1.25rem;"></i> 
                    <span class="fw-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif


        {{-- Professional Table Section --}}
        <div class="card card-premium border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 border-0">
                    <thead>
                        <tr class="border-bottom">

                            <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted">Service Details</th>
                            @if(auth()->user()->isAdmin())
                                <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-center">Provider</th>
                            @endif
                            <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-center">Duration</th>
                            <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-center">Price</th>
                            <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-center">Status</th>
                            <th class="px-4 py-3 text-uppercase tiny-font font-bold text-muted text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr class="border-bottom border-light">

                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-3 p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: var(--primary); opacity: 0.8;">
                                            <i class="ph ph-suitcase-simple text-white" style="font-size: 1.25rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold mb-0 text-dark" style="font-size: 1rem;">{{ $service->name }}</div>
                                            <div class="text-muted small line-clamp-1" style="max-width: 400px;">{{ Str::limit($service->description, 80) }}</div>
                                        </div>
                                    </div>
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td class="px-4 text-center">
                                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill border" style="background: var(--bg-body);">
                                            <i class="ph ph-user-circle mr-1 text-muted"></i>
                                            <span class="small fw-bold">{{ $service->provider->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td class="px-4 text-center">
                                    <div class="d-inline-flex align-items-center text-slate-600 font-medium small">
                                        <i class="ph ph-clock-countdown mr-1 opacity-50"></i> {{ $service->readable_duration }}
                                    </div>
                                </td>
                                <td class="px-4 text-center">
                                    <span class="fw-exrabold font-mono" style="font-size: 1.05rem;">{{ $service->formatted_price }}</span>
                                </td>
                                <td class="px-4 text-center">
                                    <span class="status-pill {{ $service->status ? 'status-active' : 'status-inactive' }}" id="status-badge-{{ $service->id }}">
                                        {{ $service->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 text-right">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-light border rounded-pill px-3 fw-bold btn-toggle-status" 
                                                data-id="{{ $service->id }}"
                                                data-url="{{ route('provider.services.toggle', $service) }}"
                                                title="Toggle Status">
                                            <i class="ph ph-power" id="toggle-icon-{{ $service->id }}"></i>
                                        </button>
 
                                        <a href="{{ route('provider.services.edit', $service) }}" class="btn btn-sm btn-light border text-primary rounded-pill px-3 fw-bold" title="Edit">
                                            <i class="ph ph-pencil"></i>
                                        </a>
 
                                        <form action="{{ route('provider.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Permanently delete this service?');" class="m-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-pill px-3 fw-bold">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? '6' : '5' }}" class="py-5 text-center border-0">
                                    <div class="py-5">
                                        <div class="mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: var(--bg-body);">
                                            <i class="ph ph-file-search text-muted" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h5 class="fw-bold">No services found</h5>
                                        <p class="text-muted small mb-4">Click "Create Service" to get started with your professional catalog.</p>
                                        <a href="{{ route('provider.services.create') }}" class="btn btn-primary rounded-pill px-4">
                                            Add First Service
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $services->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-toggle-status').on('click', function() {
        const btn = $(this);
        const icon = btn.find('i');
        const serviceId = btn.data('id');
        const url = btn.data('url');
        const badge = $(`#status-badge-${serviceId}`);

        // Loading state
        btn.prop('disabled', true);
        icon.attr('class', 'ph ph-spinner-gap animate-spin');

        $.ajax({
            url: url,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    // Update badge
                    badge.text(response.status_text)
                         .attr('class', `status-pill ${response.status_class}`);
                    
                    // Show success toast
                    Swal.fire({
                        title: response.status ? 'Activated!' : 'Deactivated!',
                        text: response.message,
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update status.',
                    icon: 'error',
                    toast: true,
                    position: 'top-end'
                });
            },
            complete: function() {
                // Restore state
                btn.prop('disabled', false);
                icon.attr('class', 'ph ph-power');
            }
        });
    });
});
</script>
<style>
.animate-spin {
    animation: spin 1s linear infinite;
    display: inline-block;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush

@endsection