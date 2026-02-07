@extends('layouts.app')

@section('title', 'Localidades')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Localidades</h2>
                <p class="text-sm text-gray-500">Gerenciamento de Igrejas, Barracões e outras unidades físicas.</p>
            </div>
            
            @can('create', App\Models\Igreja::class)
                <a href="{{ route('igrejas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors flex items-center gap-2 shadow-sm text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nova Localidade
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
            <form action="{{ route('igrejas.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Buscar</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nome, Código CCB ou Cidade..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm pl-9">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div>
                    <label for="tipo" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tipo</label>
                    <select name="tipo" id="tipo" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Todos</option>
                        @foreach($tipos as $id => $nome)
                            <option value="{{ $id }}" {{ request('tipo') == $id ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2 px-4 rounded-md transition-colors text-sm shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['search', 'tipo', 'status']))
                        <a href="{{ route('igrejas.index') }}" class="ml-2 text-red-600 hover:text-red-800 text-xs font-bold uppercase tracking-wider underline self-center">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nome</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cidade/UF</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Admin</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($igrejas as $igreja)
                        <tr class="hover:bg-gray-50 transition-colors even:bg-gray-50/30">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $igreja->cod_siga ?? $igreja->codigo_ccb }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                {{ $igreja->nome }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">
                                {{ $igreja->cidade }} - {{ $igreja->uf }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $tipos[$igreja->id_tipo] ?? 'Outro' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ $igreja->local->nome ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('update', $igreja)
                                        <a href="{{ route('igrejas.edit', $igreja) }}" class="text-blue-600 hover:text-blue-900 p-1 hover:bg-blue-50 rounded transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    @endcan
                                    @can('delete', $igreja)
                                        <form action="{{ route('igrejas.destroy', $igreja) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta localidade?');">
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
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <p>Nenhuma localidade encontrada.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $igrejas->links() }}
        </div>
    </div>
@endsection