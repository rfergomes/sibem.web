<div class="hidden md:flex flex-col w-64 bg-white border-r border-gray-200 shrink-0 h-full z-20">
    <!-- Logo Area -->
    <div class="h-16 flex items-center px-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center gap-3">
            <div
                class="w-8 h-8 bg-[#1e40af] rounded-lg text-white flex items-center justify-center font-bold text-lg shadow-blue-200 shadow-md">
                S
            </div>
            <div>
                <h1 class="font-bold text-gray-800 tracking-tight leading-none">SIBEM</h1>
                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">Inventário</span>
            </div>
        </div>
    </div>

    <!-- Administration Selector -->
    <div class="px-4 py-4 border-b border-gray-100 bg-gray-50/50">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-2">Administração
            Ativa</label>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="w-full flex items-center justify-between gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-blue-300 transition-all text-sm group">
                <div class="flex items-center gap-2 truncate text-left">
                    <div class="w-2 h-2 rounded-full bg-green-500 shrink-0"></div>
                    <span
                        class="font-bold text-gray-700 truncate">{{ session('current_local_name', 'Global / Principal') }}</span>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden py-1">

                @php
                    $authorizedLocais = auth()->user()->authorized_locais;
                @endphp

                @if($authorizedLocais && $authorizedLocais->count() > 0)
                    <div class="max-h-60 overflow-y-auto">
                        @foreach($authorizedLocais as $l)
                            <form action="{{ route('admin.switch-local') }}" method="POST">
                                @csrf
                                <input type="hidden" name="local_id" value="{{ $l->id }}">
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-xs text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition-colors flex items-center gap-2 {{ session('current_local_id') == $l->id ? 'bg-blue-50/50 font-bold' : '' }}">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full {{ session('current_local_id') == $l->id ? 'bg-blue-600' : 'bg-gray-300' }}">
                                    </div>
                                    {{ $l->nome }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <div class="px-4 py-2 text-[10px] text-gray-400 italic">Nenhuma administração disponível</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-2">Principal</p>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('inventarios.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('inventarios.*') ? 'bg-blue-50 text-blue-700' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('inventarios.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                </path>
            </svg>
            Inventários
        </a>

        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-6">Gestão</p>

        <a href="{{ route('bens.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('bens.*') ? 'bg-blue-50 text-blue-700' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('bens.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            Bens & Ativos
        </a>

        <p class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-6">Cadastros</p>

        <a href="{{ route('igrejas.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('igrejas.*') ? 'bg-blue-50 text-blue-700' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('igrejas.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Localidades
        </a>

        <a href="{{ route('setores.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('setores.*') ? 'bg-blue-50 text-blue-700' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('setores.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            Setores
        </a>

        <a href="{{ route('dependencias.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('dependencias.*') ? 'bg-blue-50 text-blue-700' : '' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dependencias.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                </path>
            </svg>
            Dependências
        </a>

        <!-- Admin Only Section -->
        @if(auth()->user()->perfil_id <= 2)
            <div class="mt-8 mb-2 px-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Administração</div>
            <a href="{{ route('admin.access-requests.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('admin.access-requests.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.access-requests.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                    </path>
                </svg>
                Solicitações
                @if(isset($pendingAccessRequests) && $pendingAccessRequests > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full ml-auto">
                        {{ $pendingAccessRequests }}
                    </span>
                @endif
            </a>

            <a href="{{ route('locais.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('locais.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('locais.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Administrações
            </a>

            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium group transition-all {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"
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
    <div class="p-4 border-t border-gray-200">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 group hover:bg-gray-50 p-2 rounded-lg transition-colors">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                {{ substr(auth()->user()->nome, 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-700 transition-colors">
                    {{ auth()->user()->nome }}
                </p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </a>
    </div>
</div>