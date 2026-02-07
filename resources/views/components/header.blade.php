<header class="h-14 bg-[#111827] flex items-center justify-between px-6 shrink-0 shadow-lg text-white">
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ADM</span>
            <span class="text-sm font-bold tracking-tight">
                {{ Session::get('current_local_name') ? strtoupper(Session::get('current_local_name')) : 'GLOBAL' }}
            </span>
        </div>
        <div class="h-4 w-[1px] bg-gray-700"></div>
        <div class="flex items-center gap-2">
            <div
                class="w-2 h-2 rounded-full {{ Session::has('current_tenant_id') ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-gray-500' }}">
            </div>
            <span class="text-[10px] text-gray-400">
                Conectado: <span
                    class="text-gray-200">{{ Config::get('database.connections.tenant.database', 'sibem_adm') }}</span>
            </span>
        </div>
    </div>

    <div class="flex items-center gap-6">
        @if(auth()->check() && (auth()->user()->perfil === 'admin_sistema' || auth()->user()->perfil === 'admin_regional'))
            <button @click="$dispatch('open-switch-modal')"
                class="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-widest">
                Trocar Administração
            </button>
            <div class="h-4 w-[1px] bg-gray-700"></div>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="text-xs font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">Sair</button>
        </form>
    </div>
</header>