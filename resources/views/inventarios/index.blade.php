@extends('layouts.app')

@section('title', 'Inventários')

@section('content')
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">📊 Gestão de Inventários</h1>
            <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow transition duration-200 ease-in-out flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Inventário
            </button>
        </div>

        @if($inventarios->isEmpty())
            <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-lg">
                <p class="text-gray-500 italic">Nenhum inventário realizado nesta localidade.</p>
            </div>
        @else
            <!-- Inventory List (Same as before but filtered for clarity) -->
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ref.</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Igreja</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Sincronismo</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventarios as $inv)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap font-bold">{{ $inv->codigo_unico }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $inv->igreja_nome }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inv->status == 'aberto' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    @if($inv->is_sincronizado)
                                        <span class="text-green-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg> Ok</span>
                                    @else
                                        <span class="text-amber-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg> Pendente</span>
                                    @endif
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                                    @if($inv->status == 'aberto')
                                        @if($inv->is_sincronizado)
                                            <a href="{{ route('scan.show', $inv->id) }}"
                                                class="text-blue-600 hover:text-blue-900 font-semibold mr-3 underline">🛍️ Conferir</a>
                                        @else
                                            <a href="{{ route('inventarios.show', $inv->id) }}"
                                                class="text-amber-600 hover:text-amber-900 font-semibold mr-3 underline">⚠️ Sincronizar</a>
                                        @endif
                                    @endif
                                    <a href="{{ route('inventarios.show', $inv->id) }}"
                                        class="text-gray-600 hover:text-gray-900 font-semibold underline">📄 Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Modal for New Inventory (Refined UI) -->
    <div id="modal-create"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-2 md:p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl overflow-hidden flex flex-col md:flex-row h-[90vh] max-h-[900px]">
            <!-- Sidebar Selection (Left) -->
            <div class="md:w-2/3 p-8 bg-gray-50 overflow-y-auto border-r h-full">
                <h2 class="text-xl font-black text-gray-800 mb-6 uppercase tracking-tight flex items-center">
                    <span class="bg-blue-600 text-white p-2 rounded mr-3"><svg class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg></span>
                    Selecionar Casa de Oração
                </h2>

                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <!-- Sector Filter -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Setor</label>
                        <select id="filter-setor" onchange="filterIgrejas()"
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:ring-blue-500">
                            <option value="">Todos os Setores</option>
                            @foreach($setores as $setor)
                                <option value="{{ $setor }}">{{ $setor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Search Input -->
                    <div class="w-full md:w-2/3">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pesquisar</label>
                        <input type="text" id="search-igreja" onkeyup="filterIgrejas()" placeholder="Nome ou código..."
                            class="w-full border-gray-200 rounded-lg shadow-sm focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2" id="igreja-list">
                    @forelse($igrejas as $igreja)
                        <button type="button" onclick="selectIgreja('{{ $igreja->id }}', '{{ $igreja->nome }}')"
                            data-setor="{{ $igreja->setor }}" data-nome="{{ $igreja->nome }}"
                            class="igreja-item text-left p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-500 hover:ring-2 hover:ring-blue-100 transition-all flex justify-between items-center group">
                            <div>
                                <p class="font-bold text-gray-800 group-hover:text-blue-700">
                                    {{ str_pad($igreja->id, 4, '0', STR_PAD_LEFT) }} - {{ $igreja->nome }}
                                </p>
                                <p class="text-xs text-gray-400">Setor: {{ $igreja->setor ?? 'Não definido' }} | Status: ATIVO
                                </p>
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 text-blue-600 transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </button>
                    @empty
                        <div class="p-12 text-center bg-white rounded-2xl border-2 border-dashed border-gray-100">
                            <div class="bg-amber-50 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-800">Nenhuma igreja encontrada</h3>
                            <p class="text-xs text-gray-500 mt-2">Não existem casas de oração vinculadas à sua administração
                                atual no banco de dados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Form Details (Right) -->
            <div class="md:w-1/3 p-8 border-l bg-white flex flex-col h-full overflow-y-auto">
                <form action="{{ route('inventarios.store') }}" method="POST" id="form-inventory">
                    @csrf
                    <input type="hidden" name="igreja_id" id="selected-igreja-id" required>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Localidade Selecionada</h3>
                        <div id="church-display"
                            class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-blue-900 font-black text-center italic">
                            Nenhuma selecionada
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ano</label>
                            <input type="number" name="ano" value="{{ date('Y') }}"
                                class="w-full border-gray-200 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mês</label>
                            <select name="mes" class="w-full border-gray-200 rounded-lg" required>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
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
                            required>{{ Auth::user()->nome }}</textarea>
                        <p class="text-[10px] text-gray-400 mt-1">Dica: Pule linha (Enter) para cada nome aparecer
                            separadamente no local de assinaturas.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-black text-blue-900 uppercase mb-2 tracking-wider">Inventariantes
                            (Equipe do Sistema)</label>
                        <div
                            class="border-2 border-gray-100 rounded-xl p-3 bg-gray-50 max-h-40 overflow-y-auto shadow-inner">
                            @foreach($usuarios as $user)
                                <label
                                    class="flex items-center gap-2 mb-2 cursor-pointer hover:bg-blue-50 p-1 rounded transition">
                                    <input type="checkbox" name="inventariantes_list[]" value="{{ $user->nome }}"
                                        class="rounded text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-bold text-gray-700">{{ $user->nome }}</span>
                                </label>
                            @endforeach
                        </div>
                        <input type="hidden" name="inventariante" id="final-inventariante">
                        <p class="text-[10px] text-gray-400 mt-1">Selecione os usuários que participarão da conferência.</p>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                            class="w-1/3 py-3 border border-gray-200 rounded-xl font-bold text-gray-400 hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="w-2/3 py-3 bg-blue-600 rounded-xl font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 transition flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                </path>
                            </svg>
                            Salvar
                        </button>
                    </div>
                </form>

                <div class="mt-8 p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-[10px] text-amber-700 font-bold uppercase mb-1">Nota Importante</p>
                    <p class="text-xs text-amber-600 leading-relaxed italic">Após salvar, será obrigatório o sincronismo dos
                        dados via importação SIGA para iniciar a conferência.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterIgrejas() {
            const setor = document.getElementById('filter-setor').value;
            const search = document.getElementById('search-igreja').value.toLowerCase();
            const items = document.querySelectorAll('.igreja-item');

            items.forEach(item => {
                const itemSetor = item.getAttribute('data-setor');
                const itemNome = item.getAttribute('data-nome').toLowerCase();

                const matchesSetor = !setor || itemSetor === setor;
                const matchesSearch = !search || itemNome.includes(search);

                if (matchesSetor && matchesSearch) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        function selectIgreja(id, nome) {
            document.getElementById('selected-igreja-id').value = id;
            document.getElementById('church-display').innerText = nome;

            // Highlight selected
            document.querySelectorAll('.igreja-item').forEach(i => i.classList.remove('border-blue-500', 'ring-2', 'ring-blue-100', 'bg-blue-50'));
            event.currentTarget.classList.add('border-blue-500', 'ring-2', 'ring-blue-100', 'bg-blue-50');
        }

        document.getElementById('form-inventory').addEventListener('submit', function (e) {
            const checkboxes = document.querySelectorAll('input[name="inventariantes_list[]"]:checked');
            const selected = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('final-inventariante').value = selected.join("\n");
        });
    </script>
@endsection