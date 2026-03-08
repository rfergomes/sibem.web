@extends('layouts.app')

@section('title', 'Editar Inventário #' . $inventario->codigo_unico)

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">✏️ Editar Inventário</h1>
            <a href="{{ route('inventarios.index') }}" class="text-gray-500 hover:text-gray-700 font-bold">Voltar</a>
        </div>

        <form action="{{ route('inventarios.update', $inventario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Localidade Selecionada</h3>
                <div class="p-4 bg-gray-100 border border-gray-200 rounded-xl text-gray-700 font-black text-center italic">
                    {{ $inventario->igreja_nome ?? $inventario->id_igreja }} (Não editável)
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ano</label>
                    <input type="number" name="ano" value="{{ old('ano', $inventario->ano) }}"
                        class="w-full border-gray-200 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mês</label>
                    <select name="mes" class="w-full border-gray-200 rounded-lg" required>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('mes', $inventario->mes) == $i ? 'selected' : '' }}>
                                {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-black text-blue-900 uppercase mb-2 tracking-wider">Responsáveis
                    (Assinaturas)</label>
                <textarea name="responsavel" rows="3"
                    class="w-full border-2 border-gray-100 rounded-xl text-sm focus:border-blue-500 shadow-inner p-3 bg-gray-50 italic"
                    placeholder="Digite os nomes dos responsáveis, um por linha para o relatório..."
                    required>{{ old('responsavel', $inventario->responsavel) }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1">Dica: Pule linha (Enter) para cada nome aparecer separadamente no local de assinaturas.</p>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-black text-blue-900 uppercase mb-2 tracking-wider">Inventariantes
                    (Equipe do Sistema)</label>
                <div class="border-2 border-gray-100 rounded-xl p-3 bg-gray-50 max-h-40 overflow-y-auto shadow-inner">
                    @php
                        $inventariantesArray = explode("\n", $inventario->inventariante);
                        $inventariantesArray = array_map('trim', $inventariantesArray);
                    @endphp
                    @foreach($usuarios as $user)
                        <label class="flex items-center gap-2 mb-2 cursor-pointer hover:bg-blue-50 p-1 rounded transition">
                            <input type="checkbox" name="inventariantes_list[]" value="{{ $user->nome }}"
                                {{ in_array($user->nome, $inventariantesArray) ? 'checked' : '' }}
                                class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-bold text-gray-700">{{ $user->nome }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="hidden" name="inventariante" id="final-inventariante" value="{{ old('inventariante', $inventario->inventariante) }}">
                <p class="text-[10px] text-gray-400 mt-1">Selecione os usuários que participarão da conferência.</p>
            </div>

            <div class="flex justify-end gap-4 mt-8">
                <a href="{{ route('inventarios.index') }}"
                    class="py-3 px-6 border border-gray-200 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="py-3 px-8 bg-blue-600 rounded-xl font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7"></path>
                    </svg>
                    Atualizar Inventário
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        const checkboxes = document.querySelectorAll('input[name="inventariantes_list[]"]:checked');
        const selected = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('final-inventariante').value = selected.join("\n");
    });
</script>
@endsection
