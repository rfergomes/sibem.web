@extends('layouts.app')

@section('title', 'Editar Igreja')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Editar Igreja</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('igrejas.update', $igreja->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Local -->
                <div class="mb-4">
                    <label for="local_id" class="block text-sm font-medium text-gray-700">Administração</label>
                    <select name="local_id" id="local_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                        @foreach($locais as $local)
                            <option value="{{ $local->id }}" {{ $igreja->local_id == $local->id ? 'selected' : '' }}>
                                {{ $local->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Codigo CCB -->
                <div class="mb-4">
                    <label for="codigo_ccb" class="block text-sm font-medium text-gray-700">Código CCB</label>
                    <input type="text" name="codigo_ccb" id="codigo_ccb"
                        value="{{ old('codigo_ccb', $igreja->codigo_ccb) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Apenas números. Ex: 220317</p>
                </div>

                <!-- Nome -->
                <div class="mb-4 col-span-2">
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Igreja</label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome', $igreja->nome) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                </div>

                <!-- Cidade -->
                <div class="mb-4">
                    <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                    <input type="text" name="cidade" id="cidade" value="{{ old('cidade', $igreja->cidade) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                </div>

                <!-- UF -->
                <div class="mb-4">
                    <label for="uf" class="block text-sm font-medium text-gray-700">UF</label>
                    <select name="uf" id="uf"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required>
                        <option value="">Selecione...</option>
                        @foreach($ufs as $estado)
                            <option value="{{ $estado['sigla'] }}" {{ old('uf', $igreja->uf) == $estado['sigla'] ? 'selected' : '' }}>{{ $estado['sigla'] }} - {{ $estado['nome'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bairro -->
                <div class="mb-4">
                    <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro</label>
                    <input type="text" name="bairro" id="bairro" value="{{ old('bairro', $igreja->bairro) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <!-- Setor -->
                <div class="mb-4">
                    <label for="setor" class="block text-sm font-medium text-gray-700">Setor</label>
                    <select name="setor" id="setor"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Selecione...</option>
                        @foreach($setores as $setorItem)
                            <option value="{{ $setorItem->nome }}" {{ old('setor', $igreja->setor) == $setorItem->nome ? 'selected' : '' }}>{{ $setorItem->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Razão Social -->
                <div class="mb-4 col-span-2">
                    <label for="razao_social" class="block text-sm font-medium text-gray-700">Razão Social</label>
                    <input type="text" name="razao_social" id="razao_social"
                        value="{{ old('razao_social', $igreja->razao_social) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <!-- CNPJ -->
                <div class="mb-4">
                    <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj', $igreja->cnpj) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <!-- Logradouro -->
                <div class="mb-4">
                    <label for="logradouro" class="block text-sm font-medium text-gray-700">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro"
                        value="{{ old('logradouro', $igreja->logradouro) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <!-- Numero -->
                <div class="mb-4">
                    <label for="numero" class="block text-sm font-medium text-gray-700">Número</label>
                    <input type="text" name="numero" id="numero" value="{{ old('numero', $igreja->numero) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <!-- Observacao -->
                <div class="mb-4 col-span-2">
                    <label for="observacao" class="block text-sm font-medium text-gray-700">Observação</label>
                    <textarea name="observacao" id="observacao" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('observacao', $igreja->observacao) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('igrejas.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
@endsection