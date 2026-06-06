<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Macacino')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')

    <style>
        #welcome-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }

        #welcome-overlay.visible {
            opacity: 1;
            pointer-events: auto;
        }

        #welcome-text {
            text-align: center;
            font-family: 'Cormorant Garamond', 'Palatino Linotype', Georgia, serif;
            font-style: italic;
            font-weight: 300;
            color: #fff;
            line-height: 1.5;
            letter-spacing: 0.03em;
            animation: welcome-rise 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        #welcome-text .line1 {
            display: block;
            font-size: clamp(18px, 3.5vw, 28px);
            color: rgba(255, 255, 255, 0.72);
            margin-bottom: 6px;
        }

        #welcome-text .line2 {
            display: block;
            font-size: clamp(28px, 6vw, 52px);
            color: #fff;
            text-shadow: 0 2px 40px rgba(255, 220, 140, 0.35);
        }

        #welcome-text .line3 {
            display: block;
            font-size: clamp(22px, 4vw, 34px);
            color: rgba(255, 200, 180, 0.90);
            margin-top: 4px;
        }

        @keyframes welcome-rise {
            from {
                transform: translateY(18px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        #highlights-sidebar.collapsed,
        .highlights-sidebar.collapsed {
            display: none !important;
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            background: transparent !important;
        }

        #ai-panel[hidden],
        .ai-panel[hidden] {
            display: none !important;
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            background: transparent !important;
        }

        #highlights-sidebar.collapsed+#left-resizer,
        #left-resizer.hidden,
        .resizer.hidden {
            display: none !important;
            width: 0 !important;
        }
    </style>
</head>

<body>
    <div id="welcome-overlay" hidden>
        <div id="welcome-text"></div>
    </div>

    @yield('body')

    <div id="toast-container"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div style="flex: 1;">${message}</div>
                <button onclick="this.parentElement.style.animation = 'toast-out 0.3s forwards'; setTimeout(() => this.parentElement.remove(), 300)" 
                        style="background:none; border:none; color:inherit; font-size:18px; cursor:pointer; padding:0 4px; line-height:1; margin-left: 10px;">
                    &times;
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'toast-out 0.3s forwards';
                    setTimeout(() => { toast.remove(); }, 300);
                }
            }, 4000);
        }

        window.addEventListener('load', () => {
            @if(session('success')) showToast("{{ session('success') }}", "success"); @endif
            @if(session('error')) showToast("{{ session('error') }}", "error"); @endif
            @if(session('warning')) showToast("{{ session('warning') }}", "warning"); @endif

                @if(session('welcome_user'))
                    const username = "{{ session('welcome_user') }}";
                    const overlay = document.getElementById('welcome-overlay');
                    const textEl = document.getElementById('welcome-text');

                    textEl.innerHTML = `
                                          <span class="line1">welcome my beloved friend,</span>
                                          <span class="line2">${username}</span>
                                          <span class="line3">♡</span>
                                        `;

                    overlay.removeAttribute('hidden');
                    requestAnimationFrame(() => overlay.classList.add('visible'));

                    setTimeout(() => {
                        overlay.classList.remove('visible');
                        setTimeout(() => overlay.setAttribute('hidden', ''), 600);
                    }, 2800);
                @endif
        });
    </script>

    @yield('scripts')
</body>

</html>