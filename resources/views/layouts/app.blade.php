<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIBEM CCB')</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#111827">
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tom Select (Searchable Selects) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Custom Theme */
        .ts-wrapper.single .ts-control,
        .ts-wrapper.multi .ts-control {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            box-shadow: none;
            transition: all 0.3s;
            min-height: auto;
        }

        .ts-wrapper.focus .ts-control,
        .ts-wrapper.single.focus .ts-control {
            background-color: white;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .ts-dropdown {
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            font-size: 0.875rem;
            overflow: hidden;
        }

        .ts-dropdown .ts-dropdown-content {
            max-height: 240px;
        }

        .ts-dropdown [data-selectable].option {
            padding: 0.5rem 1rem;
            color: #374151;
        }

        .ts-dropdown [data-selectable].option:hover,
        .ts-dropdown [data-selectable].option.active {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .ts-wrapper .ts-control input {
            font-size: 0.875rem;
            color: #374151;
        }

        .ts-wrapper.multi .ts-control .item {
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 0.375rem;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .ts-dropdown .no-results {
            padding: 0.75rem 1rem;
            color: #9ca3af;
        }

        /* Prevent Tom Select from overlapping SweetAlert notifications */
        .ts-wrapper {
            position: relative;
            z-index: auto;
            /* Override default tom-select border/background that causes the card effect */
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .ts-dropdown {
            z-index: 999 !important;
            /* Below SweetAlert2 (1060+) */
        }

        /* SweetAlert must always be on top */
        .swal2-container {
            z-index: 10060 !important;
        }

        .swal2-popup {
            z-index: 10061 !important;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        window.showAlert = (title, icon = 'info', text = '') => {
            Swal.fire({
                title,
                text,
                icon,
                confirmButtonColor: '#1e40af',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2 rounded-lg font-bold'
                }
            });
        };

        window.confirmAction = (title, text, callback) => {
            Swal.fire({
                title,
                text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1e40af',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2 rounded-lg font-bold mx-2',
                    cancelButton: 'px-6 py-2 rounded-lg font-bold mx-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof callback === 'function') {
                        callback();
                    } else {
                        console.error('Callback is not a function');
                    }
                }
            });
        };

        // Auto-show flash messages
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))             Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif
            @if(session('error'))             Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
            @endif
            @if(session('warning'))             Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
            @endif
            @if(session('status'))             Toast.fire({ icon: 'info', title: '{{ session('status') }}' });
            @endif
        });

        // === Tom Select: Global Searchable Select Init ===
        window.initTomSelects = function (scope) {
            const root = scope || document;
            root.querySelectorAll('select:not([data-no-tomselect]):not(.ts-hidden-accessible)').forEach(el => {
                if (el.tomselect) return; // Already initialized
                // Never initialize inside SweetAlert containers
                if (el.closest('.swal2-popup') || el.closest('.swal2-container')) return;
                // Skip selects inside Alpine x-model without explicit opt-in
                if (el.hasAttribute('x-model') && !el.hasAttribute('data-tomselect')) return;

                const isMultiple = el.multiple;
                new TomSelect(el, {
                    plugins: isMultiple ? ['remove_button'] : [],
                    create: false,
                    maxOptions: 500,
                    placeholder: el.getAttribute('placeholder') || (isMultiple ? 'Selecione um ou mais...' : 'Selecione...'),
                    searchField: ['text'],
                    noResultsText: 'Nenhum resultado encontrado',
                    render: {
                        no_results: function (data, escape) {
                            return '<div class="no-results">Nenhum resultado para "<em>' + escape(data.input) + '"</em></div>';
                        }
                    }
                });
            });
        };

        // Delay init slightly so SweetAlert toast renders first (avoids visual overlap)
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.initTomSelects(), 150);
        });
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.4s ease-out;
        }

        .animate-scaleIn {
            animation: scaleIn 0.3s ease-out;
        }
    </style>
</head>

<body class="h-full overflow-hidden font-sans text-gray-900 antialiased"
    x-data="{ sidebarOpen: window.innerWidth >= 768 }">
    <div class="flex h-screen bg-gray-50 overflow-hidden">

        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <x-header />

            <!-- Subheader -->
            <div
                class="bg-gray-50 border-b border-gray-200 px-6 py-3 md:px-8 md:py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
                <h1 class="font-bold text-gray-700 text-lg uppercase tracking-wider">@yield('title')</h1>
                <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('dashboard') }}" title="Dashboard"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                            </a>
                        </li>

                        @if(isset($breadcrumbs))
                            @foreach($breadcrumbs as $breadcrumb)
                                <li>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-300 mx-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        <a href="{{ $breadcrumb['url'] }}"
                                            class="text-sm font-medium {{ $breadcrumb['is_current'] ? 'text-gray-800 font-bold' : 'text-gray-500 hover:text-gray-700' }}">
                                            {{ $breadcrumb['title'] }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ol>
                </nav>
            </div>

            <!-- View Content -->
            <div class="flex-1 overflow-y-auto p-6 md:p-8">
                @yield('content')
            </div>

            <!-- Footer -->
            <x-footer />
        </main>

        <!-- Switch Adm Modal -->
        <div x-data="{ open: false, locais: [], loading: false }"
            @open-switch-modal.window="open = true; loading = true; fetch('{{ route('adm.list') }}').then(r => r.json()).then(d => { locais = d; loading = false; })"
            x-show="open" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">

            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" x-show="open"
                x-transition.opacity></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-scaleIn"
                        @click.away="open = false">
                        <div class="bg-gray-50 border-b px-4 py-3 sm:px-6 flex justify-between items-center">
                            <h3 class="text-base font-bold leading-6 text-gray-900" id="modal-title">Trocar
                                Administração</h3>
                            <button @click="open = false" type="button"
                                class="text-gray-400 hover:text-gray-500 font-bold text-2xl">&times;</button>
                        </div>
                        <div class="px-4 py-5 sm:p-6 max-h-[60vh] overflow-y-auto">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Selecione o
                                destino</p>

                            <div x-show="loading" class="text-center py-8 text-gray-500 text-sm">Carregando...</div>

                            <div class="space-y-2">
                                <template x-for="local in locais" :key="local.id">
                                    <button @click="
                                        fetch('{{ route('adm.switch') }}', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                            body: JSON.stringify({ local_id: local.id })
                                        }).then(r => r.json()).then(d => { if(d.success) window.location.reload(); })
                                    "
                                        class="w-full text-left p-4 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50 transition-all flex items-center justify-between group">
                                        <div>
                                            <span class="block font-bold text-gray-800" x-text="local.nome"></span>
                                            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-tighter"
                                                x-text="'Banco: ' + local.db_name"></span>
                                        </div>
                                        <span
                                            class="opacity-0 group-hover:opacity-100 text-blue-500 transition-opacity">→</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button @click="open = false" type="button"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @stack('scripts')
</body>

</html>