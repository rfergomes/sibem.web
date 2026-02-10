<header class="h-16 bg-[#033D60] flex items-center justify-between px-6 shrink-0 shadow-lg text-white z-30 relative">
    <!-- Left: Toggle & Mobile Info -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-gray-300 focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <!-- Mobile Context (Visible only on small screens) -->
        <div class="md:hidden flex flex-col leading-tight">
            <span class="text-[10px] opacity-75 uppercase font-bold tracking-wider">ADM</span>
            <span
                class="text-xs font-bold truncate max-w-[150px]">{{ Session::get('current_local_name', 'Local') }}</span>
        </div>
    </div>

    <!-- Center: Organization Name -->
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 hidden md:block pointer-events-none">
        <span
            class="font-serif text-xl tracking-wide font-bold text-white shadow-sm uppercase whitespace-nowrap">Congregação
            Cristã no Brasil</span>
    </div>

    <!-- Right: Admin Info + Icons -->
    <div class="flex items-center gap-6">
        <!-- Admin Info (Desktop) -->
        <div class="hidden lg:flex flex-col items-end leading-tight text-right">
            <div class="flex items-center gap-2 text-[10px] font-bold tracking-widest text-blue-200 uppercase">
                <span>{{ Session::get('current_regional_name', 'Regional') }}</span>
                <span class="text-blue-400">|</span>
                <span>{{ Session::get('current_local_name', 'Localidade') }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-[10px] opacity-75 bg-black/20 px-2 py-0.5 rounded-full mt-0.5">
                <div
                    class="w-1.5 h-1.5 rounded-full {{ Session::has('current_tenant_id') ? 'bg-green-400' : 'bg-gray-400' }}">
                </div>
                <span class="font-mono">{{ Config::get('database.connections.tenant.database', 'sibem_adm') }}</span>
            </div>
        </div>

        <div class="h-8 w-[1px] bg-white/10 hidden lg:block"></div>

        <div class="flex items-center gap-4">
            <!-- Notification -->
            <button class="relative text-white/80 hover:text-white transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
                <span
                    class="absolute top-0.5 right-0.5 block w-2.5 h-2.5 rounded-full bg-red-500 ring-2 ring-[#033D60]"></span>
            </button>

            <!-- User Dropdown -->
            <div class="relative" x-data="{ userOpen: false }">
                <button @click="userOpen = !userOpen" class="flex items-center gap-2 focus:outline-none">
                    <div
                        class="w-10 h-10 rounded-full bg-white text-[#033D60] flex items-center justify-center font-bold border-2 border-white/20 hover:border-white/40 transition-colors text-lg">
                        {{ substr(auth()->user()->nome ?? 'U', 0, 1) }}
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="userOpen" @click.away="userOpen = false"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-1 text-gray-700 ring-1 ring-black ring-opacity-5 origin-top-right z-50">

                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->nome ?? 'Usuário' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 text-gray-700">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Meu Perfil
                        </a>

                        @if(auth()->check() && (auth()->user()->perfil === 'admin_sistema' || auth()->user()->perfil === 'admin_regional'))
                            <button @click="$dispatch('open-switch-modal'); userOpen = false"
                                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 text-blue-600">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Trocar Administração
                            </button>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>