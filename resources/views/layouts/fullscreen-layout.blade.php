<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | E-SPT Login</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (document.body) {
                    document.body.classList.add('dark', 'bg-gray-900');
                }
            } else {
                document.documentElement.classList.remove('dark');
                if (document.body) {
                    document.body.classList.remove('dark', 'bg-gray-900');
                }
            }
        })();
    </script>
</head>

<body x-data="{ 'loaded': true}" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    @yield('content')

    <!-- Tom Select JS & Global Init -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select dark mode and theme styling matching our design */
        .ts-wrapper {
            width: 100% !important;
            display: block !important;
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
            height: auto !important;
        }
        .ts-wrapper.single .ts-control {
            background-color: transparent !important;
            background-image: none !important;
            border-color: #d1d5db !important; /* border-gray-300 */
            border-radius: 0.5rem !important; /* rounded-lg */
            height: 2.75rem !important; /* h-11 */
            padding: 0.5rem 1rem !important;
            display: flex !important;
            align-items: center !important;
            font-size: 0.875rem !important; /* text-sm */
            color: #1f2937 !important; /* text-gray-800 */
        }
        .dark .ts-wrapper.single .ts-control {
            border-color: #374151 !important; /* dark:border-gray-700 */
            color: rgba(255, 255, 255, 0.9) !important; /* dark:text-white/90 */
        }
        .ts-wrapper.single .ts-control input {
            color: #1f2937 !important;
        }
        .dark .ts-wrapper.single .ts-control input {
            color: white !important;
        }
        .ts-dropdown {
            border-radius: 0.5rem !important;
            border-color: #e5e7eb !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            background: #ffffff !important;
        }
        .dark .ts-dropdown {
            background: #111827 !important; /* dark bg-gray-900 */
            border-color: #374151 !important;
        }
        .ts-dropdown .active {
            background-color: #3b82f6 !important; /* bg-blue-500 */
            color: #ffffff !important;
        }
        .dark .ts-dropdown .option {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        /* Style search input inside dropdown */
        .ts-dropdown .dropdown-input-wrap {
            padding: 8px !important;
            border-bottom: 1px solid #f3f4f6 !important;
        }
        .dark .ts-dropdown .dropdown-input-wrap {
            border-bottom: 1px solid #374151 !important;
        }
        .ts-dropdown .dropdown-input {
            width: 100% !important;
            height: 2.25rem !important;
            border-radius: 0.375rem !important;
            border: 1px solid #d1d5db !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            background-color: #ffffff !important;
            color: #1f2937 !important;
            outline: none !important;
        }
        .dark .ts-dropdown .dropdown-input {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
            color: #ffffff !important;
        }
        .ts-dropdown .dropdown-input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const initTomSelects = () => {
                document.querySelectorAll('select:not(.no-search):not(.tomselected)').forEach(select => {
                    if (select.closest('[x-show="!open"]') || select.closest('.hidden')) return;

                    try {
                        new TomSelect(select, {
                            create: false,
                            plugins: ['dropdown_input'],
                            onChange: function(val) {
                                // Update native select value
                                select.value = val;
                                // Dispatch change event for Alpine.js x-model
                                select.dispatchEvent(new Event('change', { bubbles: true }));
                                // Directly update Alpine data context if found
                                const alpineEl = select.closest('[x-data]');
                                if (alpineEl && alpineEl.__x && alpineEl.__x.$data) {
                                    if ('dinasSelected' in alpineEl.__x.$data) {
                                        alpineEl.__x.$data.dinasSelected = val;
                                    }
                                }
                            }
                        });
                    } catch (e) {
                        // Suppress initialization warnings
                    }
                });
            };

            // Initial run
            setTimeout(initTomSelects, 100);

            // Listen to any events that modify DOM
            const observer = new MutationObserver(() => {
                initTomSelects();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
</body>

@stack('scripts')

</html>
