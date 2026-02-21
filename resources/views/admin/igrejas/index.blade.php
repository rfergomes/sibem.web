@extends('layouts.app')

@section('title', 'Localidades')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 animate-fadeIn">
        
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Localidades</h2>
                <p class="text-xs text-gray-500">Gerenciamento de Igrejas, Barracões e outras unidades físicas.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <x-view-toggle storage-key="igrejas_view_mode" />
                @can('create', App\Models\Igreja::class)
                    <a href="{{ route('igrejas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-4 rounded-lg transition-colors flex items-center gap-2 shadow-sm text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nova Localidade
                    </a>
                @endcan
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50/50 rounded-lg p-3 mb-4 border border-gray-200 shadow-inner">
            <form action="{{ route('igrejas.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-3 flex-wrap">
                
                {{-- Search --}}
                <div class="w-full md:flex-1 min-w-[200px]">
                    <label for="search" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Buscar</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nome, Código, ID, Cidade..." 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pl-9 py-1.5 transition-all hover:border-blue-300">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Regional Filter (Admin Only) --}}
                @if(auth()->user()->perfil_id == 1)
                <div class="w-full md:w-48">
                    <label for="regional_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Regional</label>
                    <select name="regional_id" id="regional_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5 transition-all hover:border-blue-300">
                        <option value="">Todas</option>
                        @foreach($regionais as $regional)
                            <option value="{{ $regional->id }}" {{ request('regional_id') == $regional->id ? 'selected' : '' }}>{{ $regional->nome }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Admin Local Filter --}}
                <div class="w-full md:w-48">
                    <label for="local_id" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Administração</label>
                    <select name="local_id" id="local_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5 transition-all hover:border-blue-300">
                        <option value="">Todas</option>
                        @foreach($locais as $local)
                            <option value="{{ $local->id }}" {{ request('local_id') == $local->id ? 'selected' : '' }}>{{ $local->nome }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- UF Filter --}}
                <div class="w-full md:w-24">
                    <label for="uf" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">UF</label>
                    <select name="uf" id="uf" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5 transition-all hover:border-blue-300">
                        <option value="">Todos</option>
                        @foreach($ufs as $uf)
                             <option value="{{ $uf['sigla'] }}" {{ request('uf') == $uf['sigla'] ? 'selected' : '' }}>{{ $uf['sigla'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo Filter --}}
                <div class="w-full md:w-40">
                    <label for="tipo" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo</label>
                    <select name="tipo" id="tipo" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-1.5 transition-all hover:border-blue-300">
                        <option value="">Todos</option>
                        @foreach($tipos as $id => $nome)
                            <option value="{{ $id }}" {{ request('tipo') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="w-full md:w-auto flex items-center gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-4 rounded-lg transition-colors text-sm shadow-sm flex items-center justify-center gap-1 border border-transparent whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'tipo', 'status', 'regional_id', 'local_id', 'uf']))
                        <a href="{{ route('igrejas.index') }}" class="bg-white hover:bg-red-50 text-red-600 border border-gray-200 font-semibold py-1.5 px-3 rounded-lg transition-colors text-xs flex items-center justify-center shadow-sm" title="Limpar Filtros">
                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLE VIEW --}}
        <div data-view-content="table" class="hidden overflow-hidden rounded-lg border border-gray-200 animate-fadeIn delay-75">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Localidade</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cidade/UF</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Administração</th>
                        <th scope="col" class="px-3 py-2 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($igrejas as $igreja)
                        <tr class="hover:bg-blue-50/50 transition-colors even:bg-gray-50/30 group">
                            <td class="px-3 py-1.5 whitespace-nowrap text-sm font-medium text-gray-900 border-l-2 border-transparent group-hover:border-blue-500">
                                {{ $igreja->cod_siga ?? ($igreja->codigo_ccb ?? '-') }}
                            </td>
                            <td class="px-3 py-1.5 whitespace-nowrap text-sm text-gray-700">
                                {{ $igreja->nome }}
                            </td>
                            <td class="px-3 py-1.5 whitespace-nowrap text-sm text-gray-600">
                                {{ $igreja->cidade }} - <span class="font-semibold">{{ $igreja->uf }}</span>
                            </td>
                            <td class="px-3 py-1.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-medium rounded-full {{ ($igreja->tipoImovel->nome ?? '') == 'TEMPLO' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $igreja->tipoImovel->nome ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 whitespace-nowrap text-xs text-gray-500">
                                {{ $igreja->local->nome ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-1.5 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    @can('update', $igreja)
                                        <a href="{{ route('igrejas.edit', $igreja) }}" class="text-gray-400 hover:text-blue-600 p-1 rounded-full hover:bg-blue-50 transition-all" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    @endcan
                                    @can('delete', $igreja)
                                        <form action="{{ route('igrejas.destroy', $igreja) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta localidade?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 transition-all" title="Excluir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <p>Nenhuma localidade encontrada com os filtros selecionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD VIEW --}}
        <div data-view-content="card" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($igrejas as $igreja)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all hover:border-blue-200 group flex flex-col h-full">
                        <div class="p-4 flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ ($igreja->tipoImovel->nome ?? '') == 'TEMPLO' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-500 border border-gray-100' }}">
                                    {{ $igreja->tipoImovel->nome ?? 'N/A' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-gray-900 text-sm mb-1 truncate" title="{{ $igreja->nome }}">{{ $igreja->nome }}</h3>
                            <p class="text-[10px] text-gray-500 font-semibold uppercase tracking-wider mb-2">
                                Código: {{ $igreja->cod_siga ?? ($igreja->codigo_ccb ?? '-') }}
                            </p>

                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-2 mb-3">
                                <p class="text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $igreja->cidade }} - {{ $igreja->uf }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $igreja->local->nome ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $igreja->setor ?? 'SETOR NÃO DEFINIDO' }}
                                </p>
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-2">
                            @can('update', $igreja)
                                <a href="{{ route('igrejas.edit', $igreja) }}"
                                    class="flex-1 text-center py-1.5 rounded bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 text-gray-600 text-xs font-semibold transition-all shadow-sm">
                                    Editar
                                </a>
                            @endcan

                            @can('delete', $igreja)
                                <form action="{{ route('igrejas.destroy', $igreja) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir esta localidade?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full py-1.5 rounded bg-white border border-gray-200 hover:border-red-300 hover:text-red-600 text-gray-600 text-xs font-semibold transition-all shadow-sm">
                                        Excluir
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500 bg-gray-50 rounded-xl border border-gray-100 border-dashed">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-sm font-medium">Nenhuma localidade encontrada com os filtros selecionados.</p>
                    </div>
                @endforelse
            </div>
            
        </div>
        
        <div class="mt-4">
            {{ $igrejas->links() }}
        </div>
    </div>
@endsection