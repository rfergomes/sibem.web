@extends('layouts.app')

@section('title', 'Nova Igreja')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Nova Igreja</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('igrejas.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Local -->
                <div class="mb-4">
                    <label for="local_id" class="block text-xs font-bold text-gray-500 uppercase mb-2">Administração</label>
                    <select name="local_id" id="local_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        required>
                        <option value="">Selecione...</option>
                        @foreach($locais as $local)
                            <option value="{{ $local->id }}" data-uf="{{ $local->uf }}">{{ $local->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo de Imóvel -->
                <div class="mb-4">
                    <label for="id_tipo" class="block text-xs font-bold text-gray-500 uppercase mb-2">Tipo de Imóvel</label>
                    <select name="id_tipo" id="id_tipo"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        required>
                        <option value="">Selecione...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('id_tipo') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Codigo CCB -->
                <div class="mb-4">
                    <label for="codigo_ccb" class="block text-xs font-bold text-gray-500 uppercase mb-2">Código CCB</label>
                    <input type="text" name="codigo_ccb" id="codigo_ccb" value="{{ old('codigo_ccb') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Apenas números. Ex: 220317</p>
                </div>

                <!-- Nome -->
                <div class="mb-4 col-span-2">
                    <label for="nome" class="block text-xs font-bold text-gray-500 uppercase mb-2">Nome da Igreja</label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        required>
                </div>

                <!-- Cidade -->
                <div class="mb-4">
                    <label for="cidade" class="block text-xs font-bold text-gray-500 uppercase mb-2">Cidade</label>
                    <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        required>
                </div>

                <!-- UF -->
                <div class="mb-4">
                    <label for="uf" class="block text-xs font-bold text-gray-500 uppercase mb-2">UF</label>
                    <select name="uf" id="uf"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        required>
                        <option value="">Selecione...</option>
                        @foreach($ufs as $estado)
                            <option value="{{ $estado['sigla'] }}" {{ old('uf') == $estado['sigla'] ? 'selected' : '' }}>
                                {{ $estado['sigla'] }} - {{ $estado['nome'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bairro -->
                <div class="mb-4">
                    <label for="bairro" class="block text-xs font-bold text-gray-500 uppercase mb-2">Bairro</label>
                    <input type="text" name="bairro" id="bairro" value="{{ old('bairro') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                </div>

                <!-- Razão Social -->
                <div class="mb-4 col-span-2">
                    <label for="razao_social" class="block text-xs font-bold text-gray-500 uppercase mb-2">Razão
                        Social</label>
                    <input type="text" name="razao_social" id="razao_social" value="{{ old('razao_social') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                </div>

                <!-- CNPJ -->
                <div class="mb-4">
                    <label for="cnpj" class="block text-xs font-bold text-gray-500 uppercase mb-2">CNPJ</label>
                    <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                </div>

                <!-- Logradouro -->
                <div class="mb-4">
                    <label for="logradouro" class="block text-xs font-bold text-gray-500 uppercase mb-2">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" value="{{ old('logradouro') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                </div>

                <!-- Numero -->
                <div class="mb-4">
                    <label for="numero" class="block text-xs font-bold text-gray-500 uppercase mb-2">Número</label>
                    <input type="text" name="numero" id="numero" value="{{ old('numero') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                </div>

                <!-- Observacao -->
                <div class="mb-4 col-span-2">
                    <label for="observacao" class="block text-xs font-bold text-gray-500 uppercase mb-2">Observação</label>
                    <textarea name="observacao" id="observacao" rows="3"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">{{ old('observacao') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('igrejas.index') }}"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-gray-500 hover:bg-gray-600 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    Salvar
                </button>
            </div>
        </form>
    </div>

    {{-- Script to fetch Local Info and Enforce UF --}}
    <script>
        document.getElementById('local_id').addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const ufInfo = selected.getAttribute('data-uf');
            const ufSelect = document.getElementById('uf');

            if (ufInfo) {
                ufSelect.value = ufInfo;
                // Highlight variables check
                ufSelect.parentElement.classList.add('ring-2', 'ring-blue-500', 'ring-opacity-50', 'rounded-md');
                setTimeout(() => {
                    ufSelect.parentElement.classList.remove('ring-2', 'ring-blue-500', 'ring-opacity-50', 'rounded-md');
                }, 2000);
            }
        });
    </script>
@endsection