@extends('layouts.app')

@section('title', 'Inventário #' . $inventario->codigo_unico)

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div x-data="{ showImport: false }">
            @if(!$inventario->is_sincronizado)
                <!-- Mandatory Sync Screen -->
                <div
                    class="bg-gray-100 min-h-[600px] flex flex-col md:flex-row shadow-2xl rounded-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                    <!-- Left Panel: Data Table -->
                    <div class="md:w-3/4 bg-[#E1E1E1] p-0 flex flex-col">
                        <div
                            class="bg-[#004A80] text-white p-2 px-4 flex justify-between items-center text-xs font-bold uppercase tracking-widest">
                            <span>Lista Geral de Bens (Visualização)</span>
                            <span>ERP SIBEM</span>
                        </div>
                        <div class="flex-grow overflow-auto p-4">
                            <div
                                class="bg-white shadow rounded p-8 text-center flex flex-col items-center justify-center h-full">
                                <div class="bg-blue-50 p-6 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-5-8l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-black text-gray-800 mb-2">Aguardando Sincronização</h2>
                                <p class="text-gray-500 text-sm max-w-sm mx-auto">Para carregar a lista oficial de bens movéis
                                    desta localidade, você deve importar o arquivo gerado pelo sistema SIGA.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel: Actions -->
                    <div class="md:w-1/4 bg-[#D4D4D4] border-l border-gray-300 p-6 flex flex-col">
                        <div class="text-center mb-8">
                            <h3 class="text-lg font-black text-[#004A80]">
                                {{ str_pad($inventario->id_igreja, 4, '0', STR_PAD_LEFT) }}</h3>
                            <p class="text-xs font-bold text-gray-600 uppercase">{{ $inventario->igreja_nome }}</p>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div class="bg-white p-3 rounded shadow-sm border-t-4 border-[#004A80]">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Inventário</p>
                                <p class="text-sm font-black text-gray-800">{{ $inventario->mes }}/{{ $inventario->ano }}</p>
                            </div>
                            <div class="bg-white p-3 rounded shadow-sm">
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Responsáveis</p>
                                <div class="text-[10px] text-gray-600 italic leading-tight">
                                    {!! nl2br(e($inventario->responsavel)) !!}</div>
                            </div>
                        </div>

                        <!-- Sticky Note -->
                        <div class="relative bg-[#FFEF82] p-6 shadow-lg mb-8 transition-transform hover:scale-105 rotate-1">
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <div class="w-10 h-3 bg-red-400 opacity-50 rounded-full"></div>
                            </div>
                            <p class="text-xs font-bold text-[#8C7A00] leading-relaxed">
                                É de suma importância obter os dados atualizados do SIGA. Você deve efetuar a importação dos
                                bens agora.
                            </p>
                        </div>

                        <div class="mt-auto space-y-3">
                            <button @click="showImport = true"
                                class="w-full bg-[#004A80] hover:bg-[#003B66] text-white p-4 rounded shadow-lg font-black text-sm flex flex-col items-center group transition">
                                <svg class="w-6 h-6 mb-1 group-hover:scale-110 transition" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-5-4l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                IMPORTAR BENS
                            </button>

                            <button disabled
                                class="w-full bg-gray-400 text-white p-4 rounded shadow font-black text-sm flex items-center justify-center opacity-50 cursor-not-allowed">
                                CONTINUAR <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </button>

                            <a href="{{ route('inventarios.index') }}"
                                class="w-full py-2 text-center text-xs font-bold text-gray-500 hover:text-red-600">CANCELAR
                                OPERAÇÃO</a>
                        </div>
                    </div>
                </div>

                <!-- Import Modal -->
                <div x-show="showImport" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4">
                    <div @click.away="showImport = false"
                        class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden animate-in zoom-in duration-200">
                        <div class="bg-blue-900 p-6 text-white text-center">
                            <h3 class="text-xl font-black uppercase tracking-widest mb-1 italic">Sincronismo de Dados</h3>
                            <p class="text-xs opacity-70">Selecione o arquivo Excel exportado do sistema SIGA.</p>
                        </div>

                        <form id="importForm" action="{{ route('bens.import') }}" method="POST" enctype="multipart/form-data"
                            class="p-8 space-y-6">
                            @csrf
                            <input type="hidden" name="inventario_id" value="{{ $inventario->id }}">

                            <div
                                class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-blue-500 transition-colors group cursor-pointer relative">
                                <input type="file" name="file" class="absolute inset-0 opacity-0 cursor-pointer" required
                                    onchange="this.nextElementSibling.innerText = this.files[0].name">
                                <p class="text-gray-400 text-sm font-bold uppercase group-hover:text-blue-600">Clique para
                                    selecionar o arquivo</p>
                                <p class="text-[9px] text-gray-300 mt-2">Formatos suportados: .XLSX, .XLS, .CSV</p>
                            </div>

                            <div class="flex gap-4">
                                <button type="button" @click="showImport = false"
                                    class="w-1/3 py-3 font-bold text-gray-400 hover:text-gray-600">VOLTAR</button>
                                <button type="submit"
                                    class="w-2/3 py-3 bg-blue-600 text-white rounded-xl font-black shadow-lg shadow-blue-200 hover:bg-blue-700 transition">SINCRONIZAR
                                    AGORA</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- Full Report View -->
                <div class="space-y-6 animate-in slide-in-from-bottom duration-500">
                    <div
                        class="bg-white shadow rounded-lg p-6 flex justify-between items-center bg-gradient-to-r from-white to-blue-50">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">📋 Relatório de Inventário</h1>
                            <p class="text-gray-500">{{ $inventario->codigo_unico }} | {{ $inventario->igreja_nome }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($inventario->status == 'aberto')
                                @if($inventario->is_sincronizado)
                                    <a href="{{ route('scan.show', $inventario->id) }}"
                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                                        🛍️ INICIAR CONFERÊNCIA
                                    </a>
                                @endif
                            @else
                                <div class="flex gap-2">
                                    <a href="{{ route('inventarios.print', $inventario->id) }}" target="_blank"
                                        class="bg-gray-800 text-white px-4 py-2 rounded font-bold text-xs uppercase shadow hover:bg-black transition">
                                        📜 Ata FOR.AI.22
                                    </a>
                                    <a href="{{ route('inventarios.custom_report', $inventario->id) }}" target="_blank"
                                        class="bg-blue-900 text-white px-4 py-2 rounded font-bold text-xs uppercase shadow hover:bg-blue-800 transition">
                                        📊 Relatório Geral
                                    </a>
                                </div>
                                <div class="flex gap-2 mt-2">
                                     <a href="{{ route('report.14-5', $inventario->id) }}" target="_blank"
                                        class="bg-indigo-900 text-white px-4 py-2 rounded font-bold text-xs uppercase shadow hover:bg-indigo-800 transition flex items-center gap-2">
                                        📄 SIGA 14.5 (Ata)
                                    </a>
                                    <a href="{{ route('report.14-8', $inventario->id) }}" target="_blank"
                                        class="bg-indigo-900 text-white px-4 py-2 rounded font-bold text-xs uppercase shadow hover:bg-indigo-800 transition flex items-center gap-2">
                                        📅 SIGA 14.8 (Mensal)
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 font-mono text-center">
                        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-gray-400">
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Bens Iniciais</p>
                            <p class="text-3xl font-black text-gray-800">
                                {{ $inventario->detalhes()->where('status_leitura', '!=', 'novo_sistema')->count() }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                            <p class="text-[10px] text-gray-400 font-bold uppercase text-green-600">Localizados</p>
                            <p class="text-3xl font-black text-green-700">
                                {{ $inventario->detalhes()->where('status_leitura', 'encontrado')->count() }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-amber-500">
                            <p class="text-[10px] text-gray-400 font-bold uppercase text-amber-500">Pendentes</p>
                            <p class="text-3xl font-black text-amber-600">
                                {{ $inventario->detalhes()->where('status_leitura', 'nao_encontrado')->count() }}</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                            <p class="text-[10px] text-gray-400 font-bold uppercase text-blue-500">Novos / Diferença</p>
                            <p class="text-3xl font-black text-blue-700">
                                {{ $inventario->detalhes()->where('status_leitura', 'novo_sistema')->count() }}</p>
                        </div>
                    </div>

                    <!-- Divergences Table -->
                    <div class="bg-white shadow rounded-xl overflow-hidden">
                        <div class="bg-gray-800 px-6 py-4 text-white flex justify-between items-center">
                            <h3 class="font-bold text-xs tracking-widest uppercase">📄 Lista Geral de Ocorrências (Divergências)
                            </h3>
                            <div class="text-[10px] opacity-70 italic">Sincronizado em:
                                {{ $inventario->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="p-0">
                            @php $divergencias = App\Models\Divergencia::where('inventario_id', $inventario->id)->get(); @endphp
                            @if($divergencias->isEmpty())
                                <div class="p-10 text-center text-gray-300 italic">Nenhuma divergência registrada nesta localidade.
                                </div>
                            @else
                                <table class="min-w-full">
                                    <thead class="bg-gray-50 border-b">
                                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">
                                            <th class="px-6 py-3 text-left">Cód</th>
                                            <th class="px-6 py-3 text-left">Bem / Etiqueta</th>
                                            <th class="px-6 py-3 text-left">Descrição da Ocorrência</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 italic">
                                        @foreach($divergencias as $div)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-xs font-black text-blue-600">{{ $div->codigo_divergencia }}
                                                </td>
                                                <td class="px-6 py-4 text-xs font-mono font-bold text-gray-800">#{{ $div->id_bem }}</td>
                                                <td class="px-6 py-4 text-xs text-gray-600 uppercase">{{ $div->descricao }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@push('scripts')
    <script>
        document.getElementById('importForm')?.addEventListener('submit', function(e) {
            Swal.fire({
                title: 'Sincronizando Dados',
                text: 'Aguarde enquanto processamos a lista de bens... Este processo pode levar alguns segundos dependendo do volume de dados.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
@endpush
@endsection