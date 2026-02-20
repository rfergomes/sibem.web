@extends('layouts.app')

@section('title', 'Administrações')

@section('content')
    <div class="animate-fadeIn p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Administrações (Locais)</h1>
                <p class="text-xs text-gray-500">Gerencie as unidades administrativas e seus bancos de dados.</p>
            </div>

            <div class="flex items-center gap-4">
                <x-view-toggle storage-key="locais_view_mode" />
                @can('create', App\Models\Local::class)
                    <a href="{{ route('locais.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-4 rounded-lg shadow-sm transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nova Administração
                    </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div
                class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200 font-medium text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div
                class="mb-4 p-3 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200 font-medium text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                {{ session('warning') }}
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-gray-50/50 rounded-lg p-3 mb-6 border border-gray-200 shadow-inner">
            <form action="{{ route('locais.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">

                {{-- Search --}}
                <div class="md:col-span-4">
                    <label for="search"
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Buscar</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Nome, Cidade ou DB..."
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pl-9 py-1.5 transition-all hover:border-blue-300">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Regional Filter (Admin Only) --}}
                @if(auth()->user()->perfil_id == 1)
                    <div class="md:col-span-3">
                        <label for="regional_id"
                            class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Regional</label>
                        <select name="regional_id" id="regional_id"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5 transition-all hover:border-blue-300">
                            <option value="">Todas</option>
                            @foreach($regionais as $regional)
                                <option value="{{ $regional->id }}" {{ request('regional_id') == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- UF Filter --}}
                <div class="md:col-span-2">
                    <label for="uf"
                        class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">UF</label>
                    <select name="uf" id="uf"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5 transition-all hover:border-blue-300">
                        <option value="">Todos</option>
                        @foreach($ufs as $uf)
                            <option value="{{ $uf['sigla'] }}" {{ request('uf') == $uf['sigla'] ? 'selected' : '' }}>
                                {{ $uf['sigla'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="md:col-span-2 flex items-end gap-2">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-3 rounded-lg transition-colors text-sm shadow-sm flex items-center justify-center gap-1 border border-transparent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'regional_id', 'uf']))
                        <a href="{{ route('locais.index') }}"
                            class="w-1/3 bg-white hover:bg-red-50 text-red-600 border border-gray-200 font-semibold py-1.5 px-2 rounded-lg transition-colors text-xs flex items-center justify-center shadow-sm"
                            title="Limpar Filtros">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLE VIEW --}}
        <div data-view-content="table" class="hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Administração</th>
                                <th class="px-6 py-4">Regional / Localização</th>
                                <th class="px-6 py-4">Banco de Dados</th>
                                <th class="px-6 py-4">Localidades</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($locais as $local)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $local->nome }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-700 text-xs">{{ $local->regional->nome ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $local->cidade ? $local->cidade . ($local->uf ? '/' . $local->uf : '') : ($local->regional->uf ?? '') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-xs text-gray-700">{{ $local->db_name }}</div>
                                        <div class="font-mono text-xs text-gray-500">{{ $local->db_host }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $local->igrejas_count }}
                                            {{ $local->igrejas_count == 1 ? 'localidade' : 'localidades' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($local->active)
                                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-1"></span> Ativo
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block mr-1"></span> Inativo
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-3">
                                        @can('update', $local)
                                            <a href="{{ route('locais.edit', $local->id) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endcan

                                        @php
                                            $hasCredentials = !empty($local->db_host) && !empty($local->db_name) && !empty($local->db_user);
                                        @endphp
                                        <form id="provision-form-{{ $local->id }}"
                                            action="{{ route('locais.provision', $local->id) }}" method="POST">
                                            @csrf
                                            <button type="button" @if($hasCredentials)
                                                onclick="confirmAction('Inicializar Banco?', 'Isso irá rodar as migrações e dados padrão no banco desta administração.', () => document.getElementById('provision-form-{{ $local->id }}').submit())"
                                                class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-lg transition-colors"
                                            title="Provisionar" @else disabled
                                                    class="text-gray-300 cursor-not-allowed p-2 rounded-lg"
                                                    title="Credenciais do banco não informadas. Edite a administração para configurar."
                                                @endif>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 7v10l8 4m0-10L4 7m8 4v10M12 4v10m0-10l8 4m-8-4L4 7m8 4l8-4m0 10l-8 4" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <p class="text-sm font-medium">Nenhuma administração encontrada com os filtros
                                            selecionados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($locais->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $locais->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD VIEW --}}
        <div data-view-content="card" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($locais as $local)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all hover:border-blue-200 group flex flex-col h-full">
                        <div class="p-4 flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div
                                    class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $local->active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-500 border border-gray-100' }}">
                                    {{ $local->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-gray-900 text-sm mb-1 truncate" title="{{ $local->nome }}">
                                {{ $local->nome }}
                            </h3>
                            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-3">
                                {{ $local->regional->nome ?? 'Regional N/A' }}
                                <span
                                    class="text-gray-400 font-normal ml-1">{{ $local->cidade ? $local->cidade . ($local->uf ? '/' . $local->uf : '') : ($local->regional->uf ?? '') }}</span>
                            </p>

                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-2 mb-3">
                                <p class="text-xs text-blue-700 font-semibold">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $local->igrejas_count }} {{ $local->igrejas_count == 1 ? 'localidade' : 'localidades' }}
                                </p>
                            </div>

                            <div class="space-y-1.5 mb-2 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-gray-600" title="Database Name">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10l8 4m0-10L4 7m8 4v10M12 4v10m0-10l8 4m-8-4L4 7m8 4l8-4m0 10l-8 4" />
                                    </svg>
                                    <span class="font-mono text-[10px] text-gray-700 truncate">{{ $local->db_name }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-600" title="Database Host">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                    </svg>
                                    <span class="font-mono text-[10px] text-gray-700 truncate">{{ $local->db_host }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-2">
                            @can('update', $local)
                                <a href="{{ route('locais.edit', $local->id) }}"
                                    class="flex-1 text-center py-1.5 rounded bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 text-gray-600 text-xs font-semibold transition-all shadow-sm">
                                    Editar
                                </a>
                            @endcan

                            {{-- Provisioning --}}
                            @php
                                $hasCredentials = !empty($local->db_host) && !empty($local->db_name) && !empty($local->db_user);
                            @endphp
                            <form id="provision-form-{{ $local->id }}" action="{{ route('locais.provision', $local->id) }}"
                                method="POST" class="flex-1">
                                @csrf
                                <button type="button" @if($hasCredentials)
                                    onclick="confirmAction('Inicializar Banco?', 'Isso irá rodar as migrações e dados padrão no banco desta administração.', () => document.getElementById('provision-form-{{ $local->id }}').submit())"
                                    class="w-full py-1.5 rounded bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 text-gray-600 text-xs font-semibold transition-all shadow-sm"
                                @else disabled
                                        class="w-full py-1.5 rounded bg-gray-100 border border-gray-200 text-gray-400 text-xs font-semibold cursor-not-allowed"
                                    title="Credenciais do banco não informadas" @endif>
                                    Provisionar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-12 text-center text-gray-500 bg-gray-50 rounded-xl border border-gray-100 border-dashed">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <p class="text-sm font-medium">Nenhuma administração encontrada com os filtros selecionados.</p>
                    </div>
                @endforelse
            </div>

            @if($locais->hasPages())
                <div class="mt-6">
                    {{ $locais->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection