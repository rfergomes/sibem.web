@extends('layouts.app')

@section('title', 'Dependências')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Dependências</h2>
                <p class="text-sm text-gray-500">Tipos de cômodos e áreas (Sala, Cozinha, Banheiro, etc).</p>
            </div>
            
            <div class="flex items-center gap-4">
                <x-view-toggle storage-key="dependencias_view_mode" />
                @can('create', App\Models\Dependencia::class)
                    <a href="{{ route('dependencias.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center gap-2 shadow-sm text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Nova Dependência
                    </a>
                @endcan
            </div>
        </div>

        {{-- TABLE VIEW --}}
        <div data-view-content="table" class="hidden overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bens Cadastrados</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($dependencias as $dependencia)
                        <tr class="hover:bg-gray-50 transition-colors even:bg-gray-50/30">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $dependencia->nome }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">
                                @if(isset($dependencia->bens_count))
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $dependencia->bens_count }} {{ $dependencia->bens_count == 1 ? 'bem' : 'bens' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @if($dependencia->active)
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                        Ativo
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('update', $dependencia)
                                        <a href="{{ route('dependencias.edit', $dependencia) }}" class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    @endcan
                                    @can('delete', $dependencia)
                                        <form action="{{ route('dependencias.destroy', $dependencia) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors" title="Excluir">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-sm">
                                <p>Nenhuma dependência encontrada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD VIEW --}}
        <div data-view-content="card" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($dependencias as $dependencia)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all hover:border-blue-200 group flex flex-col h-full">
                        <div class="p-4 flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $dependencia->active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-500 border border-gray-100' }}">
                                    {{ $dependencia->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-gray-900 text-sm mb-3 truncate" title="{{ $dependencia->nome }}">{{ $dependencia->nome }}</h3>

                            @if(isset($dependencia->bens_count))
                                <div class="bg-blue-50 border border-blue-100 rounded-lg p-2">
                                    <p class="text-xs text-blue-700 font-semibold">
                                        <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        {{ $dependencia->bens_count }} {{ $dependencia->bens_count == 1 ? 'bem' : 'bens' }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-2">
                            @can('update', $dependencia)
                                <a href="{{ route('dependencias.edit', $dependencia) }}"
                                    class="flex-1 text-center py-1.5 rounded bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 text-gray-600 text-xs font-semibold transition-all shadow-sm">
                                    Editar
                                </a>
                            @endcan

                            @can('delete', $dependencia)
                                <form action="{{ route('dependencias.destroy', $dependencia) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir?');">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <p class="text-sm font-medium">Nenhuma dependência encontrada.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection