@extends('layouts.app')

@section('title', 'Bens - SIBEM')

@section('content')
    <div class="animate-fadeIn">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Cadastro de Bens</h1>
                <p class="text-sm text-gray-500">Gestão global de ativos da localidade.</p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('bens.import') }}"
                    class="flex items-center gap-2 bg-white text-gray-700 hover:text-gray-900 font-bold py-2.5 px-5 rounded-lg border border-gray-200 shadow-sm transition-colors">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Importar Excel
                </a>
                <!-- Create button removed as requested -->
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
            <form action="{{ route('bens.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Setor</label>
                    <select name="setor_id"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos</option>
                        @foreach($setores as $setor)
                            <option value="{{ $setor->id }}" {{ request('setor_id') == $setor->id ? 'selected' : '' }}>
                                {{ $setor->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dependência</label>
                    <select name="dependencia_id"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas</option>
                        @foreach($dependencias as $dep)
                            <option value="{{ $dep->id }}" {{ request('dependencia_id') == $dep->id ? 'selected' : '' }}>
                                {{ $dep->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Código / Bem</label>
                    <input type="text" name="codigo" value="{{ request('codigo') }}" placeholder="Buscar código..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descrição</label>
                    <input type="text" name="descricao" value="{{ request('descricao') }}" placeholder="Buscar descrição..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Filtrar
                    </button>
                    <a href="{{ route('bens.index') }}"
                        class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors"
                        title="Limpar">
                        X
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Código</th>
                            <th class="px-6 py-4">Descrição</th>
                            <th class="px-6 py-4">Dependência</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bens as $bem)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-gray-500">{{ $bem->id_bem }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $bem->descricao }}</td>
                                <td class="px-6 py-4 text-xs">
                                    @if($bem->dependencia)
                                        <div class="font-bold text-gray-700">{{ $bem->dependencia->nome }}</div>
                                        <div class="text-gray-400">{{ optional($bem->dependencia->setor)->nome }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($bem->id_status == 1)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Ativo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-blue-600 font-bold hover:underline cursor-pointer">Editar</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    Nenhum bem encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bens->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $bens->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection