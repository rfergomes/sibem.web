<div class="flex flex-col h-full">
    <!-- Logo Area -->
    <div class="h-16 flex items-center justify-center border-b border-white/10 bg-ccb-blue shrink-0">
        <img src="{{ asset('img/SIBEM_Logo_Branco.png') }}" alt="SIBEM Logo" class="h-12 object-contain">
    </div>

    <!-- Administration Selector (Tom Select with search) -->
    <div class="px-4 py-3 border-b border-gray-700 bg-ccb-blue/50 shrink-0">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Administração
            Ativa</label>
        @php $authorizedLocais = auth()->user()->authorized_locais; @endphp
        <form action="{{ route('admin.switch-local') }}" method="POST" id="admin-switcher-form">
            @csrf
            <select id="admin-switcher-select" name="local_id"
                class="w-full border border-white/20 rounded-xl px-3 py-3 font-medium transition-all duration-300 placeholder-gray-400 text-sm">
                @foreach($authorizedLocais as $l)
                    <option value="{{ $l->id }}" {{ session('current_local_id') == $l->id ? 'selected' : '' }}
                        style="color: #111827; background: white;">{{ $l->nome }}</option>
                @endforeach
            </select>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('admin-switcher-select');
                if (!el || typeof TomSelect === 'undefined') return;

                const ts = new TomSelect(el, {
                    create: false,
                    maxOptions: 500,
                    searchField: ['text'],
                    placeholder: 'Buscar administração...',
                    noResultsText: 'Nenhuma encontrada',
                    onChange: function (val) {
                        if (val) document.getElementById('admin-switcher-form').submit();
                    },
                    render: {
                        no_results: function (data, escape) {
                            return '<div class="no-results">Nenhuma para "<em>' + escape(data.input) + '"</em></div>';
                        }
                    }
                });

                // Style the wrapper for dark sidebar
                const control = el.closest('.ts-wrapper');
                if (control) {
                    const ctrl = control.querySelector('.ts-control');
                    if (ctrl) {
                        ctrl.style.background = 'rgba(255,255,255,0.07)';
                        ctrl.style.border = '1px solid rgba(255,255,255,0.2)';
                        ctrl.style.borderRadius = '0.5rem';
                        ctrl.style.color = 'white';
                        ctrl.style.fontSize = '0.875rem';
                        ctrl.style.fontWeight = '600';
                        ctrl.style.padding = '0.625rem 0.875rem';
                        ctrl.style.minHeight = '42px';
                        ctrl.style.display = 'flex';
                        ctrl.style.alignItems = 'center';
                    }
                    // Style the search input text inside
                    const input = control.querySelector('input');
                    if (input) input.style.color = 'white';
                }
            });
        </script>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-4 space-y-1"
        @click="if(window.innerWidth < 768 && $event.target.closest('a')) sidebarOpen = false">
        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-2">Principal</p>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('inventarios.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('inventarios.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('inventarios.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                </path>
            </svg>
            Inventários
        </a>

        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-6">Gestão</p>

        <a href="{{ route('bens.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('bens.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('bens.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Bens & Ativos
        </a>

        <a href="{{ route('appointments.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('appointments.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('appointments.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Agendamentos
        </a>

        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-6">Cadastros</p>

        <a href="{{ route('igrejas.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('igrejas.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('igrejas.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Localidades
        </a>

        <a href="{{ route('setores.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('setores.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('setores.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            Setores
        </a>

        <a href="{{ route('dependencias.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('dependencias.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dependencias.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                </path>
            </svg>
            Dependências
        </a>

        @if(auth()->user()->perfil_id <= 2)
            <div class="mt-8 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Administração</div>
            <a href="{{ route('admin.access-requests.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('admin.access-requests.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.access-requests.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Solicitações
                @if(isset($pendingAccessRequests) && $pendingAccessRequests > 0)
                    <span
                        class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto">{{ $pendingAccessRequests }}</span>
                @endif
            </a>

            <a href="{{ route('locais.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('locais.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('locais.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Administrações
            </a>

            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium group transition-all {{ request()->routeIs('users.*') ? 'bg-white/10 text-white' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Usuários
            </a>
        @endif
    </nav>

    <!-- User Profile -->
    <div class="h-[56px] flex flex-col justify-center border-t border-gray-700 shrink-0 px-4">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 group hover:bg-white/5 p-1 rounded-lg transition-colors">
            <div
                class="w-8 h-8 rounded-full bg-white text-ccb-blue flex items-center justify-center font-bold text-xs shrink-0">
                {{ substr(auth()->user()->nome, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-white truncate group-hover:text-white transition-colors">
                    {{ auth()->user()->nome }}
                </p>
                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>
        </a>
    </div>
</div>