@extends('settings.layout')

@section('settings_content')
<div class="p-4">
    <div class="mb-4">
        <h4 class="section-title mb-1">Theme Mode</h4>
        <p class="section-subtitle">Toggle between light and dark themes for the entire dashboard.</p>
    </div>

    <div class="settings-card border-0 shadow-sm mb-5">
        <div class="p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="setting-title font-weight-bold">Appearance Preference</span>
                    <span class="setting-desc">Choose your preferred visual style for the platform.</span>
                </div>

                <div class="d-flex align-items-center gap-3 p-2 px-4" style="border-radius: 12px; background: var(--bg-body); border: 1px solid var(--border-color);">
                    <span class="fw-bold {{ auth()->user()->theme_preference === 'light' ? 'text-success' : 'text-muted' }} small">
                        <i class="ph ph-sun me-1"></i> Light
                    </span>
                    
                    <label class="premium-switch mb-0">
                        <input type="checkbox" id="theme-toggle" {{ auth()->user()->theme_preference === 'dark' ? 'checked' : '' }}>
                        <span class="premium-slider"></span>
                    </label>
                    
                    <span class="fw-bold {{ auth()->user()->theme_preference === 'dark' ? 'text-success' : 'text-muted' }} small">
                        <i class="ph ph-moon me-1"></i> Dark
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 pb-3">
        <button type="button" class="btn btn-secondary-modern">Cancel</button>
        <button type="button" class="btn btn-save-modern" onclick="alert('Theme settings are saved instantly on change!')">Save Changes</button>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('theme-toggle').addEventListener('change', function() {
        const isDark = this.checked;
        const theme = isDark ? 'dark' : 'light';
        
        // Immediate visual feedback
        document.body.classList.toggle('dark-mode', isDark);
        document.body.classList.toggle('light-mode', !isDark);

        // Save to Database via AJAX
        fetch("{{ route('settings.appearance.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ theme_preference: theme })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Flash success color on the text labels
                console.log('Theme saved successfully');
            }
        })
        .catch(error => console.error('Error saving theme:', error));
    });
</script>
@endpush
@endsection