<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Auth' }} | BookEase</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: radial-gradient(circle at top right, #f1f5f9, #F8FAFC);
            line-height: 1.6;
            letter-spacing: -0.015em;
            -webkit-font-smoothing: antialiased;
        }
        .auth-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] min-h-screen flex items-center justify-center p-4">

    {{-- This is where your Login or Register content will inject --}}
    @yield('content')

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = document.querySelector(`#${inputId}-toggle`);
            if (!button) { // Fallback for old simple button selectors if any
                const legacyIcon = document.querySelector(`#${inputId} + button i`);
                if (legacyIcon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        legacyIcon.classList.remove('ph-eye');
                        legacyIcon.classList.add('ph-eye-slash');
                    } else {
                        input.type = 'password';
                        legacyIcon.classList.remove('ph-eye-slash');
                        legacyIcon.classList.add('ph-eye');
                    }
                    return;
                }
            }
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        }
    </script>

    {{-- Toast notifications or specific scripts can be pushed here --}}
    @stack('scripts')
</body>
</html>