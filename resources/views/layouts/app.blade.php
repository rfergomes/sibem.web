<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIBEM CCB')</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#111827">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                    callback();
                }
            });
        };

        // Auto-show flash messages
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif
            @if(session('error'))
                Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
            @endif
            @if(session('warning'))
                Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
            @endif
            @if(session('status'))
                Toast.fire({ icon: 'info', title: '{{ session('status') }}' });
            @endif
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

<body class="h-full overflow-hidden font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen bg-gray-50 overflow-hidden">

        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <x-header />

            <!-- View Content -->
            <div class="flex-1 overflow-y-auto p-6">
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