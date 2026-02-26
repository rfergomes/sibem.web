@extends('layouts.app')

@section('title', 'Tipos de Imóvel')

@section('content')
    <div class="animate-fadeIn p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Tipos de Imóvel</h1>
                <p class="text-xs text-gray-500">Gerencie os tipos de edificação (Templo, Anexo, etc.).</p>
            </div>

            <div class="flex items-center gap-4">
                <x-view-toggle storage-key="tipos_imovel_view_mode" />
                @can('create', App\Models\TipoImovel::class)
                    <a href="{{ route('tipos-imovel.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-4 rounded-lg shadow-sm transition-all flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Tipo
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

        @if(session('error'))
            <div
                class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 font-medium text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- TABLE VIEW --}}
        <div data-view-content="table" class="hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">ID</th>
                                <th class="px-6 py-4">Nome do Tipo</th>
                                <th class="px-6 py-4">Localidades</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($tipos as $tipo)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            #{{ $tipo->id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="font-bold text-gray-900">{{ $tipo->nome }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $tipo->igrejas()->count() }}
                                            {{ $tipo->igrejas()->count() == 1 ? 'localidade' : 'localidades' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-3">
                                        @can('update', $tipo)
                                            <a href="{{ route('tipos-imovel.edit', $tipo->id) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('delete', $tipo)
                                            <form id="delete-form-{{ $tipo->id }}"
                                                action="{{ route('tipos-imovel.destroy', $tipo->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="confirmAction('Excluir Tipo?', 'Esta ação não pode ser desfeita.', () => document.getElementById('delete-form-{{ $tipo->id }}').submit())"
                                                    class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                                    title="Excluir">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <p class="text-sm font-medium">Nenhum tipo de imóvel cadastrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tipos->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $tipos->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD VIEW --}}
        <div data-view-content="card" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($tipos as $tipo)
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
                                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                                    ID: {{ $tipo->id }}
                                </span>
                            </div>

                            <h3 class="font-bold text-gray-900 text-sm mb-1" title="{{ $tipo->nome }}">{{ $tipo->nome }}</h3>

                            <div class="bg-blue-50 border border-blue-100 rounded-lg p-2 mt-3">
                                <p class="text-xs text-blue-700 font-semibold">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $tipo->igrejas()->count() }}
                                    {{ $tipo->igrejas()->count() == 1 ? 'localidade' : 'localidades' }}
                                </p>
                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between gap-2">
                            @can('update', $tipo)
                                <a href="{{ route('tipos-imovel.edit', $tipo->id) }}"
                                    class="flex-1 text-center py-1.5 rounded bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 text-gray-600 text-xs font-semibold transition-all shadow-sm">
                                    Editar
                                </a>
                            @endcan

                            @can('delete', $tipo)
                                <form id="delete-form-{{ $tipo->id }}" action="{{ route('tipos-imovel.destroy', $tipo->id) }}"
                                    method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmAction('Excluir Tipo?', 'Esta ação não pode ser desfeita.', () => document.getElementById('delete-form-{{ $tipo->id }}').submit())"
                                        class="w-full py-1.5 rounded bg-white border border-gray-200 hover:border-red-300 hover:text-red-600 text-gray-600 text-xs font-semibold transition-all shadow-sm">
                                        Excluir
                                    </button>
                                </form>
                            @endcan
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
                        <p class="text-sm font-medium">Nenhum tipo de imóvel cadastrado.</p>
                    </div>
                @endforelse
            </div>

            @if($tipos->hasPages())
                <div class="mt-6">
                    {{ $tipos->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
