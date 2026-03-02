@extends('layouts.app')

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        // Sound Feedback Utility
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        const audioCtx = new AudioCtx();

        window.beep = (frequency = 440, duration = 100, type = 'sine', volume = 0.1) => {
            const oscillator = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            oscillator.type = type;
            oscillator.frequency.setValueAtTime(frequency, audioCtx.currentTime);
            gain.gain.setValueAtTime(volume, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + duration/1000);
            oscillator.connect(gain);
            gain.connect(audioCtx.destination);
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + duration/1000);
        };

        window.playSuccess = () => beep(880, 150, 'sine', 0.2);
        window.playError = () => {
            beep(220, 100, 'square', 0.1);
            setTimeout(() => beep(110, 200, 'square', 0.1), 120);
        };
    </script>
@endpush

@section('title', 'Leitor de Invent├írio')

@section('content')
    <!-- Main Container: Uses flex-1 and overflow-hidden to fit between header/footer -->
    <div x-data="inventarioScanner()" 
         @click="focusScanner()"
         class="flex flex-col lg:flex-row gap-5 h-auto lg:h-[calc(100vh-135px)] overflow-y-auto lg:overflow-hidden p-2">

        <!-- SLIM SIDEBAR: Stats & Actions -->
        <div class="lg:w-64 flex flex-col gap-4 overflow-y-auto pr-1">
            <!-- Header Card (Compact) -->
            <div class="bg-gradient-to-br from-[#004A80] to-[#003B66] text-white p-4 rounded-xl shadow-lg border border-blue-400/20">
                <p class="text-[9px] font-black uppercase opacity-60 tracking-[0.2em] mb-1">Localidade</p>
                <p class="text-sm font-black leading-tight">{{ $inventario->igreja_nome }}</p>
                <div class="mt-3 pt-3 border-t border-white/10 flex justify-between items-center text-[10px] font-bold opacity-70">
                    <span>{{ str_pad($inventario->id_igreja, 4, '0', STR_PAD_LEFT) }}</span>
                    <span>{{ $inventario->mes }}/{{ $inventario->ano }}</span>
                </div>
            </div>

            <!-- Stats Grid (More Breathable) -->
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-[8px] font-black text-gray-400 uppercase mb-1">Inicial</p>
                    <p class="text-lg font-black text-gray-800" x-text="stats.bensInicial">{{ $bensInicial }}</p>
                </div>
                <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-[8px] font-black text-gray-400 uppercase mb-1 text-amber-500">Pendente</p>
                    <p class="text-lg font-black text-amber-600" x-text="stats.pendentes">{{ $pendentes }}</p>
                </div>
                <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm">
                    <p class="text-[8px] font-black text-gray-400 uppercase mb-1 text-green-500">Conferido</p>
                    <p class="text-lg font-black text-green-600" x-text="stats.localizados">{{ $localizados }}</p>
                </div>
                <div class="bg-blue-600 p-3 rounded-xl shadow-md border border-blue-400/20">
                    <p class="text-[8px] font-black text-white/60 uppercase mb-1">Resultado</p>
                    <p class="text-lg font-black text-white" x-text="stats.resultado + '%'">{{ $resultado }}%</p>
                </div>
            </div>


            <!-- Sidebar Actions (Fixed at bottom if possible) -->
            <div class="mt-auto space-y-2 pt-2">
                <button onclick="location.reload()" class="w-full py-2 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-lg text-[9px] font-black uppercase tracking-widest transition border border-gray-200">
                    ­ƒöä Sincronizar Tudo
                </button>
                <form id="finalizeForm" action="{{ route('inventarios.finalize', $inventario->id) }}" method="POST">
                    @csrf
                    <button type="button" @click="confirmFinalize()" class="w-full py-3 bg-[#004A80] hover:bg-[#00355B] text-white rounded-xl font-black text-xs shadow-lg shadow-blue-900/20 transition uppercase tracking-widest flex items-center justify-center gap-2">
                        ­ƒÅü Finalizar
                    </button>
                </form>
            </div>
        </div>

        <!-- MAIN AREA: Scanner & History (Expanding more) -->
        <div class="flex-grow flex flex-col gap-4 overflow-hidden">
            <!-- Top Bar (More Compact) -->
            <div class="bg-white/80 backdrop-blur-md p-3 px-4 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="w-full sm:flex-grow max-w-lg">
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-xs opacity-40">­ƒôì</span>
                        <select x-model="dependenciaId" class="w-full pl-8 text-[11px] border-gray-100 rounded-lg focus:ring-0 focus:border-blue-600 font-bold uppercase tracking-tight py-2 shadow-inner bg-gray-50/80">
                            <option value="">SELECIONE LOCALIZA├ç├âO F├ìSICA...</option>
                            @foreach(App\Models\Dependencia::orderBy('nome')->get() as $dep)
                                <option value="{{ $dep->id }}">{{ str_pad($dep->id, 3, '0', STR_PAD_LEFT) }} - {{ $dep->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-6 w-full sm:w-auto">
                    <button @click="toggleCamera()" 
                            :class="cameraActive ? 'bg-red-600 text-white border-red-700' : 'bg-green-600 text-white border-green-700'"
                            class="flex-1 sm:flex-none p-2.5 px-3 sm:px-5 border rounded-lg shadow-sm hover:opacity-90 transition flex items-center justify-center gap-2 group">
                        <span class="text-xs" x-text="cameraActive ? 'ÔÅ╣´©Å' : '­ƒôÀ'"></span>
                        <span class="text-[10px] font-black uppercase tracking-widest" x-text="cameraActive ? 'Parar' : 'C├ómera'"></span>
                    </button>

                    <label class="flex items-center gap-2 cursor-pointer group">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter group-hover:text-blue-900 transition">Auto-foco</span>
                        <div class="relative inline-flex items-center scale-75">
                            <input type="checkbox" x-model="autoFocus" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004A80]"></div>
                        </div>
                    </label>

                    <button @click="showPendencias = true" class="flex-1 sm:flex-none p-2.5 px-3 sm:px-5 bg-white border border-gray-300 text-blue-900 rounded-lg shadow-sm hover:bg-gray-50 transition flex items-center justify-center gap-2 group">
                        <span class="text-xs group-hover:rotate-12 transition">­ƒôï</span>
                        <span class="text-[10px] font-black uppercase tracking-widest">Pend├¬ncias</span>
                    </button>
                </div>
            </div>

            <!-- LEITOR CARD (Slimmed padding) -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 flex flex-col overflow-hidden ring-4 ring-gray-50/50">
                <div class="p-6 pb-2 flex flex-col items-center">
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">Congrega├º├úo Crist├ú no Brasil</h2>
                        <p class="text-[9px] font-black text-blue-800/40 uppercase tracking-[0.4em]">Administra├º├úo Central</p>
                    </div>

                    <div class="w-full max-w-4xl space-y-6">
                        <!-- Camera Scanner Container -->
                        <div x-show="cameraActive" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="mb-4 overflow-hidden rounded-2xl border-4 border-blue-100 shadow-inner bg-black relative aspect-video md:aspect-auto md:h-64">
                            <div id="reader" class="w-full h-full"></div>
                            <div class="absolute inset-0 pointer-events-none border-2 border-dashed border-white/30 m-8 rounded-lg flex items-center justify-center">
                                <div class="w-full h-0.5 bg-red-500/50 absolute animate-pulse"></div>
                            </div>
                        </div>

                        <!-- Main Input -->
                        <div class="relative bg-white border-2 border-gray-100 rounded-2xl shadow-inner p-1">
                            <input type="text" 
                                   x-model="barcode" 
                                   @keyup.enter="processScan()"
                                   maxlength="12"
                                   placeholder="000000000000"
                                   class="w-full text-3xl md:text-6xl font-mono tracking-tighter text-gray-900 border-none focus:ring-0 text-center uppercase p-2 md:p-6 bg-transparent"
                                    id="scannerInput"
                                    @input="if(barcode.length === 12) processScan()"
                                    autofocus>
                            
                            <div class="absolute -bottom-5 left-0 right-0 text-center">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.4em] animate-pulse">Aguardando Leitura da Etiqueta...</p>
                            </div>
                        </div>

                        <!-- Search Section (More Horizontal) -->
                        <div class="flex gap-3">
                            <div class="flex-grow relative">
                                <span class="absolute left-4 top-3.5 text-xs opacity-20">­ƒöì</span>
                                <input type="text" 
                                       x-model="searchText" 
                                       @keyup.enter="searchByText()"
                                       placeholder="BUSCA R├üPIDA POR NOME OU DETALHES DO ITEM..." 
                                       class="w-full p-3.5 pl-10 text-[10px] border border-gray-200 rounded-xl shadow-inner uppercase font-bold focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 bg-gray-50/30">
                            </div>
                            <button @click="searchByText()" class="px-8 bg-gray-800 text-white text-[10px] font-black uppercase rounded-xl shadow-sm hover:bg-black transition-all">
                                Localizar Item
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Footer (More Compact) -->
                <div class="mt-4 bg-gray-50/80 border-t border-gray-100 p-3 px-8 flex justify-between items-center">
                    <div class="flex items-center gap-6">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-black text-gray-400 uppercase">├Ültimo Lido</span>
                            <span class="text-sm font-black text-gray-800 font-mono tracking-tighter" x-text="lastItem.barcode || '---'"></span>
                        </div>
                        <div class="h-6 w-px bg-gray-200"></div>
                        <div class="flex flex-col max-w-lg">
                            <span class="text-[8px] font-black text-gray-400 uppercase">Descri├º├úo</span>
                            <span class="text-[11px] font-bold text-gray-600 truncate uppercase" x-text="lastItem.descricao || 'Nenhum item processado'"></span>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[9px] font-black uppercase" x-show="lastItem.situacao" x-text="lastItem.situacao"></span>
                </div>
            </div>

            <!-- HISTORY LIST CARD -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col min-h-[300px]">
                <div class="bg-[#004A80] text-white p-3 px-6 flex justify-between items-center shadow-md">
                    <span class="text-[11px] font-black uppercase tracking-widest">Log de leituras recentes ({{ $inventario->mes }}/{{ $inventario->ano }})</span>
                    <span class="bg-blue-400/20 px-2 py-0.5 rounded text-[10px]" x-text="history.length + ' Itens Registrados'"></span>
                </div>
                <div class="overflow-y-auto flex-grow custom-scrollbar">
                    <table class="min-w-full text-[11px]">
                        <thead class="bg-gray-50/80 border-b sticky top-0 backdrop-blur-sm z-10">
                            <tr>
                                <th class="px-6 py-3 text-left font-black text-gray-400 uppercase tracking-tighter">Etiqueta</th>
                                <th class="px-6 py-3 text-left font-black text-gray-400 uppercase tracking-tighter">Descri├º├úo</th>
                                <th class="px-6 py-3 text-left font-black text-gray-400 uppercase tracking-tighter hidden sm:table-cell">Loc. F├¡sica</th>
                                <th class="px-6 py-3 text-left font-black text-gray-400 uppercase tracking-tighter hidden md:table-cell">Situa├º├úo</th>
                                <th class="px-6 py-3 text-center font-black text-gray-400 uppercase tracking-tighter hidden sm:table-cell">Lido</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(item, index) in history" :key="index + '-' + item.barcode">
                                <tr class="hover:bg-blue-50/50 transition-colors group"
                                    :class="item.lido ? 'text-gray-400 bg-gray-50/30' : 'text-gray-900 font-medium'">
                                    <td class="px-3 sm:px-6 py-3 font-mono text-sm tracking-tighter" 
                                        :class="item.is_cross_church ? 'text-red-700 font-black' : 'text-blue-900/40'"
                                        x-text="item.barcode"></td>
                                    <td class="px-3 sm:px-6 py-3 uppercase text-[10px] sm:text-xs" x-text="item.descricao"></td>
                                    <td class="px-6 py-3 text-gray-500 font-bold hidden sm:table-cell" x-text="item.dependencia"></td>
                                    <td class="px-6 py-3 hidden md:table-cell">
                                        <span class="px-2 py-0.5 rounded-full font-black text-[9px] uppercase tracking-tighter"
                                              :class="item.situacao.includes('DIVERG├èNCIA') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                              x-text="item.situacao"></span>
                                    </td>
                                    <td class="px-6 py-3 text-center hidden sm:table-cell">
                                        <span class="inline-block w-2 h-2 rounded-full" :class="item.lido ? 'bg-green-500 shadow-sm shadow-green-200' : 'bg-gray-300'"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PEND├èNCIAS MODAL (MOVED INSIDE SCOPE) -->
        <template x-teleport="body">
            <div x-show="showPendencias" 
                 class="fixed inset-0 bg-black bg-opacity-60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-cloak>
                <div class="bg-[#F0F0F0] rounded-lg shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] w-full max-w-6xl h-auto md:h-[650px] max-h-[90vh] border border-gray-400 flex flex-col overflow-hidden">
                    <!-- Title Bar -->
                    <div class="bg-gradient-to-b from-gray-100 to-gray-400 p-2 px-4 border-b border-gray-500 flex justify-between items-center shadow-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-blue-900 drop-shadow">­ƒô£</span>
                            <span class="text-sm font-bold text-gray-700 uppercase tracking-tight">Pend├¬ncias de Invent├írio</span>
                        </div>
                        <button @click="showPendencias = false" class="bg-red-500 hover:bg-red-700 text-white font-bold p-1 px-3 rounded shadow-inner text-[10px]">X</button>
                    </div>

                    <div class="flex flex-1 flex-col md:flex-row overflow-hidden">
                        <!-- Left: List -->
                        <div class="w-full md:w-1/2 flex flex-col border-b md:border-b-0 md:border-r border-gray-400 bg-[#E1E1E1] overflow-hidden">
                            <div class="p-3 bg-gray-200 border-b border-gray-400 flex flex-col gap-2 shadow-inner">
                                <div class="relative w-full">
                                    <input type="text" x-model="searchPendencia" placeholder="Pesquisar por C├│digo ou Descri├º├úo..." class="w-full text-[11px] border-gray-300 rounded shadow-inner pl-8 pr-2 py-2 uppercase">
                                    <span class="absolute left-3 top-2.5 opacity-40">­ƒöì</span>
                                </div>
                                <div class="flex items-center gap-4 px-1">
                                    @foreach(['pendentes' => 'Pendentes', 'encontrados' => 'Encontrados', 'tratados' => 'Tratados', 'todos' => 'Todos'] as $val => $lbl)
                                        <label class="flex items-center gap-1.5 text-[9px] font-black uppercase tracking-tighter cursor-pointer group">
                                            <input type="radio" x-model="filterStatus" value="{{ $val }}" class="text-[#004A80] focus:ring-0 w-3 h-3">
                                            <span class="group-hover:text-blue-800 transition">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex-grow overflow-y-auto">
                                <table class="w-full text-[11px] text-left border-collapse">
                                    <thead class="bg-[#004A80] text-white sticky top-0 uppercase tracking-widest text-[9px]">
                                        <tr>
                                            <th class="p-2 w-8 text-center border-r border-blue-900">
                                                <input type="checkbox" 
                                                       @change="toggleSelectAll($event.target.checked)"
                                                       :checked="filteredPendencias.length > 0 && selectedIds.length === filteredPendencias.length"
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3 h-3">
                                            </th>
                                            <th class="p-2 px-4 border-r border-blue-900 w-32">Etiqueta</th>
                                            <th class="p-2 px-4">Bem M├│vel</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <template x-for="item in filteredPendencias" :key="item.id">
                                            <tr @click="toggleSelection(item)" 
                                                class="border-b border-gray-200 cursor-pointer hover:bg-blue-50 transition"
                                                :class="selectedIds.includes(item.id) ? 'bg-[#C1D8FF] font-black' : ''">
                                                <td class="p-2 text-center border-r border-gray-100">
                                                    <input type="checkbox" 
                                                           :checked="selectedIds.includes(item.id)"
                                                           @click.stop="toggleSelection(item)"
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3 h-3">
                                                </td>
                                                <td class="p-2 px-4 border-r border-gray-100 font-mono" x-text="item.bem.id_bem"></td>
                                                <td class="p-2 px-4 truncate uppercase" x-text="item.bem.descricao"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-2 bg-gray-300 border-t border-gray-400 flex justify-between items-center text-[10px] font-black text-blue-900 px-4">
                                <span>SISTEMA DE BENS M├ôVEIS</span>
                                <span x-text="filteredPendencias.length + ' Bens Pendentes'"></span>
                            </div>
                        </div>

                        <!-- Right: Details & Actions -->
                        <div class="w-full md:w-1/2 p-3 sm:p-6 flex flex-col gap-3 sm:gap-5 overflow-y-auto bg-[#F0F0F0] shadow-inner">
                            <!-- Info Header Buttons -->
                            <div class="flex justify-between gap-1 mb-2">
                                @foreach(['CADASTRAR', 'IMPRIMIR', 'ALTERAR', 'EXCLUIR'] as $label)
                                    <div class="bg-[#003865] text-white p-1 flex-1 text-center border-2 border-gray-500 rounded-sm">
                                        <p class="text-[8px] font-black leading-none mb-1">{{ $label }}</p>
                                        <p class="text-xs font-black">{{ $tratativaCounts[strtolower($label)] ?? 0 }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white border-2 border-gray-400 shadow-sm p-2">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 p-1 mb-1 border-b">Situa├º├úo</p>
                                    <p class="text-sm font-black text-amber-600 uppercase text-center py-1" 
                                       x-text="selectedItem ? (selectedItem.tratativa && selectedItem.tratativa !== 'nenhuma' ? selectedItem.tratativa : selectedItem.status_leitura) : 'AGUARDANDO'"></p>
                                </div>
                                <div class="bg-white border-2 border-gray-400 shadow-sm p-2">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 p-1 mb-1 border-b">Etiqueta</p>
                                    <p class="text-sm font-black font-mono text-center py-1" x-text="selectedItem ? selectedItem.bem.id_bem : '0000000000'"></p>
                                </div>
                            </div>

                            <div class="bg-[#003865] text-white p-2 text-center text-[9px] font-black uppercase tracking-[0.2em] shadow">
                                Bem M├│vel
                            </div>
                            <div class="bg-white border-2 border-gray-400 shadow-inner p-4 text-[11px] font-black leading-relaxed min-h-[70px] uppercase text-gray-700 italic" 
                                 x-text="selectedItem ? selectedItem.bem.descricao : 'Selecione um item na lista ao lado para iniciar a tratativa...'"></div>

                            <div class="bg-[#003865] text-white p-2 text-center text-[9px] font-black uppercase tracking-[0.2em] shadow">
                                Observa├º├úo para o Relat├│rio
                            </div>
                            <textarea x-model="observacao" 
                                      class="w-full border-2 border-gray-400 shadow-inner rounded-sm text-[11px] font-bold p-3 h-20 focus:border-blue-600 focus:ring-0" 
                                      :class="selectedItem?.observacao?.includes('LOCALIDADE') ? 'text-red-600 border-red-300 bg-red-50' : ''"
                                      placeholder="Digite aqui as tratativas realizadas (Ex: ITEM LOCALIZADO NO FUNDO B├ìBLICO)..."></textarea>

                            <!-- New Asset Fields (Conditional) -->
                            <div x-show="tratativa === 'novo'" x-transition class="bg-amber-50 border-2 border-amber-300 p-4 rounded-sm space-y-3 shadow-md">
                                <p class="text-[9px] font-black text-amber-700 uppercase border-b border-amber-200 pb-1">­ƒôä Registro de Novo Bem (Offline/Extra-ERP)</p>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-gray-500 uppercase">Descri├º├úo do Item</label>
                                    <input type="text" x-model="novaDescricao" class="w-full p-2 text-[11px] border-gray-400 rounded-sm font-bold uppercase" placeholder="EX: VENTILADOR DE PAREDE PRETO">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-gray-500 uppercase">Depend├¬ncia Destino</label>
                                    <select x-model="novaDependencia" class="w-full p-2 text-[11px] border-gray-400 rounded-sm font-bold">
                                        <option value="">Selecione...</option>
                                        @foreach(App\Models\Dependencia::orderBy('nome')->get() as $dep)
                                            <option value="{{ $dep->id }}">{{ $dep->id }} - {{ $dep->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 mt-2 p-2 bg-blue-50 border border-blue-200 rounded">
                                    <input type="checkbox" x-model="isDoacao" id="isDoacao" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="isDoacao" class="text-[10px] font-black text-blue-700 uppercase cursor-pointer">
                                        ­ƒôä Este item ├® uma doa├º├úo? (Gera Formul├írios 14.1 e 14.2)
                                    </label>
                                </div>
                            </div>

                            <div class="bg-white p-4 border-2 border-gray-400 shadow-sm rounded-sm">
                                <p class="text-[9px] font-black text-gray-400 uppercase mb-3 border-b pb-1">Tratativa Selecionada</p>
                                <div class="grid grid-cols-3 gap-y-3 gap-x-2">
                                    @foreach([
                                        'novo' => 'Novo', 
                                        'imprimir' => 'Imprimir', 
                                        'encontrado' => 'Encontrado',
                                        'alterar' => 'Alterar', 
                                        'transferir' => 'Transferir', 
                                        'excluir' => 'Excluir'
                                    ] as $val => $txt)
                                        <label class="flex items-center gap-2 text-[10px] font-black group cursor-pointer">
                                            <input type="radio" x-model="tratativa" value="{{ $val }}" class="text-[#004A80] focus:ring-0 border-gray-400">
                                            <span class="group-hover:text-blue-700 transition">{{ $txt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex gap-4 mt-auto">
                                <button @click="showPendencias = false" class="w-1/3 bg-gray-200 border-2 border-gray-400 p-3 font-black text-gray-500 text-xs shadow hover:bg-white transition uppercase">Cancelar</button>
                                <button @click="saveTratativa()" 
                                        :disabled="selectedIds.length === 0 || !tratativa || savingTratativa"
                                        class="w-2/3 bg-[#004A80] text-white p-3 rounded-sm font-black text-sm shadow-lg hover:bg-[#003B66] hover:scale-[1.02] transition flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="!savingTratativa">
                                        <div class="flex items-center gap-2">
                                            <span class="group-hover:rotate-12 transition">­ƒÆ¥</span>
                                            <span x-text="selectedIds.length > 1 ? 'SALVAR ' + selectedIds.length + ' ITENS' : 'SALVAR TRATATIVA'"></span>
                                        </div>
                                    </template>
                                    <template x-if="savingTratativa">
                                        <div class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>PROCESSANDO...</span>
                                        </div>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function inventarioScanner() {
            return {
                barcode: '',
                displayBarcode: '',
                dependenciaId: '',
                loading: false,
                stats: {
                    bensInicial: {{ $bensInicial }},
                    pendentes: {{ $pendentes }},
                    localizados: {{ $localizados }},
                    prevista: {{ $prevista }},
                    novos: {{ $novos }},
                    bensFinal: {{ $bensFinal }},
                    resultado: {{ $resultado }},
                    tratativas: {
                        imprimir: {{ $tratativaCounts['imprimir'] }},
                        alterar: {{ $tratativaCounts['alterar'] }},
                        excluir: {{ $tratativaCounts['excluir'] }},
                        transferir: {{ $tratativaCounts['transferir'] }}
                    }
                },
                showPendencias: false,
                filterStatus: 'pendentes',
                autoFocus: true, // Default to true
                searchText: '',
                searchPendencia: '',
                selectedItem: null,
                selectedIds: [],
                tratativa: '',
                observacao: '',
                novaDescricao: '',
                novaDependencia: '',
                isDoacao: false,
                savingTratativa: false,
                cameraActive: false,
                html5QrCode: null,
                allPendencias: @json($allDetalhes),
                lastItem: {
                    barcode: '',
                    descricao: '',
                    situacao: ''
                },
                history: @json($historyInitial),

                get filteredPendencias() {
                    return this.allPendencias.filter(item => {
                        const searchLower = this.searchPendencia.toLowerCase();
                        const matchesSearch = !this.searchPendencia || 
                                           item.id_bem.toLowerCase().includes(searchLower) || 
                                           (item.bem && item.bem.descricao && item.bem.descricao.toLowerCase().includes(searchLower));
                        
                        // Decision check: If it has a tratativa, it was "treated"
                        // Treat 'nenhuma' as no decision just like null or empty
                        const hasDecision = !!item.tratativa && 
                                          item.tratativa !== 'cadastrar' && 
                                          item.tratativa !== 'nenhuma';
                        const status = item.status_leitura;
                        const isFound = status === 'encontrado';

                        // Logic for 4 filters:
                        // PENDENTES: NO reading AND NO tratativa
                        // ENCONTRADOS: YES reading (physically found)
                        // TRATADOS: NO reading AND YES tratativa
                        // TODOS: Everything
                        const matchesStatus = this.filterStatus === 'todos' || 
                                           (this.filterStatus === 'pendentes' && (status === 'nao_encontrado' || status === 'novo_sistema') && !hasDecision) ||
                                           (this.filterStatus === 'encontrados' && isFound) ||
                                           (this.filterStatus === 'tratados' && (status === 'nao_encontrado' || status === 'novo_sistema') && hasDecision);
                        
                        return matchesSearch && matchesStatus;
                    });
                },

                toggleSelection(item) {
                    if (this.selectedIds.includes(item.id)) {
                        this.selectedIds = this.selectedIds.filter(id => id !== item.id);
                        if (this.selectedItem?.id === item.id) {
                            this.resetTratativa(); // Clear when deselecting the active item
                        }
                    } else {
                        this.selectedIds.push(item.id);
                        this.selectedItem = item; 
                        
                        // Load saved data for the selected item
                        // If multiple IDs are selected, we usually keep the last one's data as a template
                        this.tratativa = (item.tratativa && item.tratativa !== 'nenhuma') ? item.tratativa : '';
                        this.observacao = item.observacao || '';
                    }
                },

                toggleSelectAll(checked) {
                    if (checked) {
                        this.selectedIds = this.filteredPendencias.map(p => p.id);
                        if (this.selectedIds.length > 0) {
                            this.selectedItem = this.filteredPendencias[0];
                        }
                    } else {
                        this.selectedIds = [];
                        this.selectedItem = null;
                    }
                },

                async searchByText() {
                    if (this.searchText.length < 3) {
                        return showAlert('Busca Auxiliar', 'warning', 'Digite pelo menos 3 caracteres para buscar.');
                    }

                    try {
                        const res = await fetch(`{{ route("scan.search_description", $inventario->id) }}?query=${this.searchText}`);
                        const data = await res.json();

                        if (data.status === 'not_found') {
                            showAlert('Busca Auxiliar', 'info', data.message);
                        } else if (data.status === 'single_match') {
                            const locationTip = data.is_global ? "\n\nÔÜá´©Å ITEM FORA DESTE INVENT├üRIO (Cria diverg├¬ncia)" : "";
                            confirmAction(
                                'Confirmar Registro',
                                `Deseja registrar a confer├¬ncia do bem:\n\n[ ${data.id_bem} ]\n${data.descricao}${locationTip}`,
                                async () => {
                                    this.barcode = data.id_bem;
                                    await this.processScan();
                                    this.searchText = '';
                                }
                            );
                        } else if (data.status === 'multiple_matches') {
                            if (data.is_global) {
                                // Show global matches in a selectable way
                                let optionsHtml = '<div class="text-left space-y-2 max-h-60 overflow-y-auto mt-4 border-t pt-4">';
                                data.items.forEach(item => {
                                    optionsHtml += `
                                        <div class="p-2 border rounded hover:bg-blue-50 cursor-pointer flex justify-between gap-2 text-xs" 
                                             onclick="window.processAuxScan('${item.id_bem}')">
                                            <span class="font-black text-blue-900">${item.id_bem}</span>
                                            <span class="flex-grow uppercase">${item.descricao}</span>
                                            <span class="text-gray-400">Ô×ò</span>
                                        </div>`;
                                });
                                optionsHtml += '</div>';

                                // Add a global helper to handle the click from SweetAlert HTML
                                window.processAuxScan = async (barcode) => {
                                    Swal.close();
                                    this.barcode = barcode;
                                    await this.processScan();
                                    this.searchText = '';
                                };

                                Swal.fire({
                                    title: 'Selecione o Bem M├│vel',
                                    html: 'Foram encontrados m├║ltiplos itens globais:' + optionsHtml,
                                    icon: 'info',
                                    showConfirmButton: false,
                                    showCloseButton: true
                                });
                            } else {
                                this.searchPendencia = this.searchText;
                                this.showPendencias = true;
                                Toast.fire({ 
                                    icon: 'info', 
                                    title: 'V├írios itens encontrados no invent├írio. Verifique na lista.' 
                                });
                            }
                        }
                    } catch (err) {
                        Toast.fire({ icon: 'error', title: 'Erro na busca por texto' });
                    }
                },

                async saveTratativa() {
                    if (this.selectedIds.length === 0 || !this.tratativa) {
                        return Toast.fire({ icon: 'warning', title: 'Selecione pelo menos um item e uma tratativa.' });
                    }

                    this.savingTratativa = true;

                    try {
                        const res = await fetch('{{ route("scan.tratativa", $inventario->id) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                detalhe_ids: this.selectedIds,
                                tratativa: this.tratativa,
                                observacao: this.observacao,
                                nova_descricao: this.novaDescricao,
                                nova_dependencia: this.novaDependencia,
                                is_doacao: this.isDoacao
                            })
                        });
                        
                        const data = await res.json();
                        if (data.status === 'success') {
                            const count = this.selectedIds.length;
                            
                            // Update local state for ALL selected IDs
                            this.selectedIds.forEach(id => {
                                // Find item in allPendencias
                                const pItem = this.allPendencias.find(p => p.id === id);
                                if (pItem) {
                                    pItem.tratativa = this.tratativa;
                                    pItem.observacao = this.observacao;

                                    if (this.tratativa === 'encontrado' || this.tratativa === 'novo') {
                                        if (pItem.status_leitura !== 'encontrado') {
                                            this.stats.localizados++;
                                            this.stats.pendentes--;
                                        }
                                        pItem.status_leitura = 'encontrado';
                                        if (pItem.bem) pItem.bem.status_leitura = 'encontrado';
                                    }

                                    // Stats check for general tratativas
                                    // (This is a bit complex as we increment for each item processed)
                                    // Normally the backend counts them all, but we sync locally for speed
                                    if (this.stats.tratativas[this.tratativa] !== undefined) {
                                        this.stats.tratativas[this.tratativa]++;
                                    }

                                    // History sync
                                    let histItem = this.history.find(h => h.barcode == pItem.id_bem);
                                    if (histItem) {
                                        histItem.situacao = 'CONFERIDO';
                                        histItem.descricao = this.novaDescricao || histItem.descricao;
                                    }
                                }
                            });

                            // Show donation PDF links if generated
                            if (data.donation_pdfs) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Doa├º├úo Registrada!',
                                    html: `
                                        <p class="mb-3">${data.message}</p>
                                        <div class="bg-blue-50 p-4 rounded border border-blue-200">
                                            <p class="font-bold text-sm mb-2">­ƒôä Formul├írios de Doa├º├úo Gerados:</p>
                                            <a href="${data.donation_pdfs.form_14_1}" target="_blank" class="block bg-blue-600 text-white px-4 py-2 rounded mb-2 hover:bg-blue-700">
                                                ­ƒôÑ Baixar Formul├írio 14.1 (Declara├º├úo de Doa├º├úo)
                                            </a>
                                            <a href="${data.donation_pdfs.form_14_2}" target="_blank" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                ­ƒôÑ Baixar Formul├írio 14.2 (Ocorr├¬ncia de Entrada)
                                            </a>
                                        </div>
                                    `,
                                    confirmButtonText: 'OK'
                                });
                            } 
                            
                            // Handle Generated Forms (14.3, 14.6, 14.7)
                            if (data.generated_forms && data.generated_forms.length > 0) {
                                let formHtml = '<div class="bg-gray-50 p-4 rounded border border-gray-200 text-left mt-4">';
                                formHtml += '<p class="font-bold text-sm mb-2 text-gray-700">­ƒôä Documentos Sugeridos:</p>';
                                data.generated_forms.forEach(f => {
                                    formHtml += `<a href="${f.url}" target="_blank" class="block bg-gray-700 text-white px-4 py-2 rounded mb-2 hover:bg-black text-[10px] font-bold uppercase transition">­ƒôÑ ${f.label}</a>`;
                                });
                                formHtml += '</div>';

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Tratativa Registrada',
                                    html: `<p>${data.message}</p>` + formHtml,
                                    confirmButtonText: 'Fechar'
                                });
                            } else if (!data.donation_pdfs) {
                                Toast.fire({ icon: 'success', title: data.message, timer: 1500 });
                            }

                            // Keep modal open, just reset selection and form
                            this.resetTratativa();
                        }
                    } catch (err) {
                        Toast.fire({ icon: 'error', title: 'Erro ao salvar tratativas' });
                    } finally {
                        this.savingTratativa = false;
                    }
                },

                resetTratativa() {
                    this.selectedItem = null;
                    this.selectedIds = [];
                    this.tratativa = '';
                    this.observacao = '';
                    this.novaDescricao = '';
                    this.novaDependencia = '';
                    this.isDoacao = false;
                },

                focusScanner() {
                    if (!this.autoFocus || this.cameraActive) return; // Respect the toggle and don't steal focus if camera is on
                    this.$nextTick(() => {
                        const input = document.getElementById('scannerInput');
                        if (input && document.activeElement !== input) input.focus();
                    });
                },

                toggleCamera() {
                    if (this.cameraActive) {
                        this.stopCamera();
                    } else {
                        this.startCamera();
                    }
                },

                startCamera() {
                    this.cameraActive = true;
                    this.$nextTick(() => {
                        this.html5QrCode = new Html5Qrcode("reader");
                        const config = { fps: 10, qrbox: { width: 250, height: 150 } };
                        
                        this.html5QrCode.start(
                            { facingMode: "environment" }, 
                            config, 
                            (decodedText) => {
                                // On Success
                                this.barcode = decodedText.replace(/[^0-9]/g, '').slice(0, 12);
                                if (this.barcode.length === 12) {
                                    this.processScan();
                                }
                            },
                            (errorMessage) => {
                                // parse error, ignore
                            }
                        ).catch((err) => {
                            console.error("Erro ao iniciar c├ómera:", err);
                            this.cameraActive = false;
                            showAlert('Erro na C├ómera', 'error', 'N├úo foi poss├¡vel acessar a c├ómera do dispositivo.');
                        });
                    });
                },

                stopCamera() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().then(() => {
                            this.cameraActive = false;
                            this.html5QrCode = null;
                            this.focusScanner();
                        }).catch(err => console.error("Erro ao parar c├ómera", err));
                    } else {
                        this.cameraActive = false;
                    }
                },

                confirmFinalize() {
                    confirmAction(
                        'Finalizar Invent├írio',
                        'ATEN├ç├âO: Deseja realmente finalizar este invent├írio? Ap├│s esta a├º├úo, NENHUMA altera├º├úo f├¡sica ou leitura poder├í ser realizada. Confirma?',
                        () => {
                            document.getElementById('finalizeForm').submit();
                        }
                    );
                },

                async processScan() {
                    if (this.loading || !this.barcode.trim()) return;
                    if (!this.dependenciaId) {
                        showAlert('Aten├º├úo', 'warning', 'Selecione a depend├¬ncia f├¡sica onde voc├¬ se encontra.');
                        this.barcode = '';
                        this.focusScanner();
                        return;
                    }

                    const codeToProcess = this.barcode.trim();
                    this.barcode = ''; // Clear immediately to prevent re-triggering from @input or Enter
                    this.loading = true;
                    this.displayBarcode = codeToProcess;

                    try {
                        const res = await fetch('{{ route("scan.process", $inventario->id) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({
                                barcode: codeToProcess,
                                id_dependencia_atual: this.dependenciaId
                            })
                        });

                        const data = await res.json();
                        this.loading = false;

                        if (data.status === 'success' || data.status === 'info' || data.status === 'warning') {
                            // Update main display
                            this.lastItem = {
                                barcode: this.barcode,
                                descricao: data.bem ? (data.bem.descricao || 'ITEM SEM CADASTRO') : 'ITEM N├âO ENCONTRADO',
                                situacao: data.status === 'success' ? 'CONFERIDO' : (data.status === 'warning' ? 'J├ü CONFERIDO' : 'DIVERG├èNCIA')
                            };

                            // Update stats
                            if (data.status === 'success') {
                                this.stats.localizados++;
                                this.stats.pendentes--;
                                this.stats.prevista = Math.round((this.stats.localizados / this.stats.bensInicial) * 100);
                            } else if (data.status === 'info') {
                                this.stats.novos++;
                            }
                            this.stats.bensFinal = this.stats.localizados + this.stats.novos;
                            this.stats.resultado = Math.round((this.stats.bensFinal / this.stats.bensInicial) * 100);

                            // Add to history list (prepend)
                            this.history.unshift({
                                barcode: this.barcode,
                                descricao: this.lastItem.descricao,
                                dependencia: this.dependenciaId,
                                situacao: this.lastItem.situacao,
                                is_cross_church: data.is_cross_church,
                                lido: true
                            });

                            // If it's a divergence, sync it to the Pendencias list immediately
                            if (data.detalhe) {
                                this.allPendencias.unshift(data.detalhe);
                            }

                            Toast.fire({ 
                                icon: data.status, 
                                title: data.message,
                                customClass: data.is_cross_church ? { popup: 'border-2 border-red-500' } : {}
                            });
                            
                            if (data.status === 'error') {
                                playError();
                            } else {
                                playSuccess();
                            }
                            
                            this.barcode = '';
                            this.focusScanner();
                        } else {
                            playError();
                            Toast.fire({ icon: 'error', title: data.message });
                            this.barcode = '';
                            this.focusScanner();
                        }
                    } catch (err) {
                        playError();
                        this.loading = false;
                        showAlert('Erro', 'error', 'Comunica├º├úo com o servidor falhou.');
                        this.focusScanner();
                    }
                }
            }
        }
    </script>
@endsection
