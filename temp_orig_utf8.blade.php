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


            <!-- SCANNER ACTION BUTTONS -->
            <div class="flex flex-col gap-2 mb-2 lg:mb-4">
                <button @click="toggleCamera()" class="w-full bg-green-500 hover:bg-green-600 rounded-xl shadow-lg border-b-4 border-green-700 text-white font-black text-xs uppercase p-3 transition flex items-center justify-center gap-2 group">
                    <span class="text-xl group-hover:scale-110 transition">­ƒôÀ</span> C├ómera Celular
                </button>
                <button @click="abrirScannerManual()" class="w-full bg-[#004A80] hover:bg-[#003B66] rounded-xl shadow-lg border-b-4 border-[#002D4C] text-white font-black text-xs uppercase p-3 transition flex items-center justify-center gap-2 group">
                    <span class="text-xl group-hover:scale-110 transition animate-pulse">­ƒôá</span> Leitor / Bipe ├Ünico
                </button>
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

        <!-- OLD MAIN AREA REMOVED - REPLACED BY PEND├èNCIAS NATIVE VIEW -->

        <!-- SCANNER MANUAL FULLSCREEN OVERLAY -->
        <div x-show="showScannerManual" 
             class="fixed inset-0 z-[140] bg-[#F5F7FA] flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             x-cloak>
            
            <!-- Header bar -->
            <div class="bg-gray-100 text-[#004A80] p-4 flex justify-between items-center shadow-sm border-b-2 border-gray-200 z-10 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="text-gray-500 animate-pulse text-2xl">­ƒôá</span>
                    <div class="flex flex-col">
                        <span class="font-black text-sm md:text-lg uppercase tracking-widest text-[#004A80]">Leitor / Digita├º├úo</span>
                        <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Aguardando C├│digo de Barras...</span>
                    </div>
                </div>
                <!-- Global Feedback Toast Overlay -->
                <div x-show="showFeedback" class="fixed top-20 left-1/2 transform -translate-x-1/2 z-[200] w-[90%] md:w-auto"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-8" x-cloak>
                    <div class="bg-gray-900/90 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 border-l-4 border-blue-500 backdrop-blur-md">
                        <div class="text-3xl" x-text="feedbackIcon"></div>
                        <div class="flex flex-col">
                            <span class="font-black text-[13px] uppercase tracking-wider text-gray-100" x-text="feedbackTitle"></span>
                            <span class="text-[11px] font-bold text-gray-300 mt-0.5" x-text="feedbackMessage"></span>
                        </div>
                    </div>
                </div>
                <button @click="fecharScannerManual()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-black px-6 py-3 rounded-lg text-[10px] md:text-sm uppercase shadow-sm flex items-center gap-2 transition">
                    <span>X</span> Voltar
                </button>
            </div>
            
            <div class="flex-grow p-4 md:p-8 flex flex-col gap-4 overflow-y-auto">
                <!-- CONTROLS & SETTINGS (Moved from Home) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 shrink-0">
                    <!-- Location Selector -->
                    <div class="bg-white p-3 border border-gray-200 shadow-sm rounded-xl">
                        <span class="text-[9px] font-black uppercase text-gray-500 mb-1 block">Localiza├º├úo Base</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs opacity-40">­ƒôì</span>
                            <select x-model="dependenciaId" class="w-full text-xs font-bold border-gray-200 rounded-lg focus:ring-0 focus:border-blue-600 uppercase tracking-tight py-1.5 shadow-inner bg-gray-50">
                                <option value="">DEFINA LOCALIZA├ç├âO F├ìSICA AQUI...</option>
                                @foreach(App\Models\Dependencia::orderBy('nome')->get() as $dep)
                                    <option value="{{ $dep->id }}">{{ str_pad($dep->id, 3, '0', STR_PAD_LEFT) }} - {{ $dep->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Search and Switches -->
                    <div class="flex flex-col gap-2">
                        <!-- Busca R├ípida Toolbar -->
                        <div class="relative bg-white border border-gray-200 rounded-xl shadow-sm flex items-center flex-grow p-1">
                            <span class="pl-2 pr-1 text-xs opacity-40">­ƒöì</span>
                            <input type="text" x-model="buscaRapida" @focus="showBuscaResultados = true" @click.away="showBuscaResultados = false" placeholder="BUSCA R├üPIDA (NOME OU C├ôDIGO)..." class="w-full bg-transparent border-none text-[10px] uppercase font-bold focus:ring-0 py-2">
                            
                            <!-- Dropdown Busca -->
                            <div x-show="showBuscaResultados && buscaRapida.length > 0" class="absolute top-12 left-0 z-50 w-full bg-white border-2 border-[#004A80] rounded-xl shadow-2xl max-h-60 overflow-y-auto">
                                <template x-if="resultadosBuscaRapida.length === 0">
                                    <div class="p-4 text-center text-[10px] font-black text-gray-400">NENHUM ENCONTRADO...</div>
                                </template>
                                <template x-for="item in resultadosBuscaRapida" :key="item.id_bem">
                                    <div @click="selecionarBuscaRapida(item)" class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition">
                                        <div class="text-[11px] font-black text-gray-800 uppercase truncate" x-text="item.bem.descricao"></div>
                                        <div class="text-[9px] font-mono font-bold text-[#004A80]" x-text="item.id_bem + ' ÔÇó ' + (item.bem.dependencia_original || '')"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Tiny Switches row -->
                        <div class="flex items-center gap-4 px-1 py-1">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <span class="text-[9px] font-black uppercase tracking-tighter text-gray-500 group-hover:text-blue-700">Travar Local</span>
                                <div class="relative inline-flex items-center scale-90">
                                    <input type="checkbox" x-model="exigirDependencia" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004A80]"></div>
                                </div>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <span class="text-[9px] font-black uppercase tracking-tighter text-gray-500 group-hover:text-blue-700">Auto-Bipe</span>
                                <div class="relative inline-flex items-center scale-90">
                                    <input type="checkbox" x-model="autoFocus" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004A80]"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Main Input Field -->
                <div class="relative bg-white border-4 border-gray-200 rounded-3xl shadow-xl p-2 shrink-0 flex items-center justify-center group overflow-hidden focus-within:border-[#004A80] focus-within:ring-4 focus-within:ring-[#004A80]/20 transition-all duration-300 min-h-[140px] md:min-h-[180px]">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-focus-within:opacity-100 transition duration-500 pointer-events-none"></div>
                    <input type="text" 
                           x-model="barcode" 
                           @keyup.enter="processScan()"
                           maxlength="12"
                           placeholder="000000000"
                           class="w-full text-5xl md:text-8xl font-black font-mono tracking-tighter text-gray-900 border-none focus:ring-0 text-center uppercase p-6 bg-transparent relative z-10"
                            id="scannerInputManualOverlay"
                            @input="if(barcode.length >= 1) { String(barcode).length === 12 ? processScan() : null; }"
                            @blur="if(showScannerManual && autoFocus) setTimeout(() => document.getElementById('scannerInputManualOverlay')?.focus(), 150)">
                    
                    <div class="absolute bottom-4 left-0 right-0 text-center pointer-events-none">
                        <p class="text-[10px] md:text-[14px] font-black tracking-[0.3em]" :class="barcode.length > 0 ? 'text-[#004A80] animate-none' : 'text-gray-300 animate-pulse'" x-text="barcode.length > 0 ? 'Pressione ENTER ou bip novamente...' : 'Aguardando Leitura...'"></p>
                    </div>
                </div>

                <!-- Last 3 items read feedback -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 flex-grow flex flex-col min-h-[250px] shrink-0">
                    <div class="bg-gray-50 border-b border-gray-200 text-gray-500 p-3 px-6 flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest">Acompanhamento Log (├Ültimos itens)</span>
                        <span class="bg-blue-100/50 text-blue-800 px-2 py-0.5 rounded text-[10px] font-black" x-text="history.length + ' LIDOS HOJE'"></span>
                    </div>
                    <div class="overflow-y-auto custom-scrollbar flex-grow p-2">
                        <div class="space-y-2">
                            <template x-if="history.length === 0">
                                <div class="h-32 flex items-center justify-center p-6 text-center text-gray-300 font-black text-sm uppercase tracking-widest border-2 border-dashed border-gray-100 rounded-xl m-4">
                                    Nenhuma leitura registrada nesta sess├úo...
                                </div>
                            </template>
                            <template x-for="(item, index) in history.slice(0, 4)" :key="index + '-' + item.barcode">
                                <div class="bg-white border-2 border-gray-100 shadow-sm p-4 rounded-xl flex items-center justify-between animate-fade-in hover:border-gray-300 transition" :class="index === 0 ? 'ring-2 ring-blue-500/30 bg-blue-50/20' : ''">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0">
                                            <span class="text-xl" :class="item.lido ? 'text-green-500' : 'text-gray-400 opacity-50'">Ô£ö´©Å</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-lg md:text-xl font-black font-mono tracking-tighter" :class="item.is_cross_church ? 'text-red-700' : 'text-[#004A80]'" x-text="item.barcode"></span>
                                            <span class="text-[11px] md:text-xs font-bold text-gray-500 uppercase tracking-wide truncate max-w-[200px] md:max-w-md" x-text="item.descricao"></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="px-3 py-1.5 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-widest text-center"
                                              :class="item.situacao.includes('DIVERG├èNCIA') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                              x-text="item.situacao"></span>
                                        <span class="text-[9px] font-black text-gray-400 mt-2 tracking-widest uppercase hidden md:block" x-text="item.dependencia ? 'LOC: ' + item.dependencia : ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CAMERA FULLSCREEN OVERLAY -->
        <div x-show="cameraActive" 
             class="fixed inset-0 z-[150] bg-black flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             x-cloak>
            <!-- Header bar for Camera -->
            <div class="bg-gray-900 text-white p-3 flex justify-between items-center shadow-md z-10">
                <div class="flex items-center gap-2">
                    <span class="text-green-500 animate-pulse">­ƒôÀ</span>
                    <span class="font-black text-sm uppercase tracking-widest text-gray-200">Scanner Ativo</span>
                </div>
                <button @click="stopCamera()" class="bg-red-600 hover:bg-red-700 text-white font-black px-4 py-2 rounded-lg text-xs uppercase shadow-lg flex items-center gap-2 transition">
                    <span>ÔÅ╣´©Å</span> Encerrar
                </button>
            </div>
            
            <!-- Camera Viewfinder -->
            <div class="flex-grow relative overflow-hidden flex items-center justify-center">
                <div id="reader" class="w-full h-full object-cover [&>video]:object-cover [&>video]:w-full [&>video]:h-full"></div>
                <div class="absolute inset-0 pointer-events-none border-[3px] border-dashed border-white/40 m-6 sm:m-12 rounded-xl flex items-center justify-center shadow-[0_0_0_9999px_rgba(0,0,0,0.5)]">
                    <div class="w-3/4 h-0.5 bg-red-500/80 absolute animate-[pulse_2s_ease-in-out_infinite] shadow-[0_0_10px_rgba(239,68,68,0.8)]"></div>
                </div>
                
                <div class="absolute bottom-10 left-0 right-0 text-center pointer-events-none drop-shadow-md">
                    <p class="text-white text-xs font-black uppercase tracking-widest bg-black/50 inline-block px-4 py-2 rounded-full backdrop-blur-sm">
                        Centralize o c├│digo de barras
                    </p>
                </div>
            </div>
        </div>

        <!-- NEW MAIN AREA: BENS / TRATATIVAS (Replaces Pendencias SPA Tab) -->
        <div class="flex-grow flex flex-col overflow-hidden bg-[#F5F7FA] rounded-xl relative shadow-md animate-fade-in border border-blue-900/20">
            <div class="w-full h-full flex flex-col overflow-hidden">

                <div class="flex-grow flex flex-col md:flex-row overflow-hidden border-2 border-[#004A80] m-1 rounded bg-white">
                    <!-- Esquerda: Tabela de Pend├¬ncias -->
                    <div class="w-full md:w-[65%] flex flex-col border-r-2 border-[#004A80]">
                        <div class="bg-gradient-to-r from-gray-100 to-white px-4 py-2 flex items-center gap-3 border-b border-gray-300 shadow-sm relative z-20">
                            <span class="text-[10px] font-black uppercase text-gray-500 tracking-tighter">Filtrar:</span>
                            <select x-model="filterStatus" class="border-gray-300 rounded text-[10px] font-bold uppercase p-1.5 focus:ring-[#004A80] focus:border-[#004A80] shadow-inner bg-white">
                                <option value="pendentes">­ƒÜ¿ N├úo Localizados (Pendente)</option>
                                <option value="encontrados">Ô£à Localizados</option>
                                <option value="tratados">­ƒôØ Tratativa Registrada</option>
                                <option value="todos">­ƒôï Mostrar Todos</option>
                            </select>
                            <input type="text" x-model="searchPendencia" placeholder="Pesquisar..." class="border-gray-300 rounded text-[10px] p-1.5 ml-auto w-40 max-w-full font-bold uppercase shadow-inner placeholder:opacity-50 focus:ring-[#004A80]">
                        </div>
                        <div class="flex-grow overflow-hidden flex flex-col min-h-0">
                            <div class="overflow-y-auto flex-1 h-full pr-1">
                                <table class="w-full text-[11px] text-left border-collapse">
                                    <thead class="bg-[#004A80] text-white sticky top-0 uppercase tracking-widest text-[9px] z-10 shadow-sm">
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
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <template x-for="item in filteredPendencias" :key="item.id">
                                            <tr @click="toggleSelection(item)" 
                                                class="cursor-pointer hover:bg-blue-50 transition"
                                                :class="selectedIds.includes(item.id) ? 'bg-[#C1D8FF] font-black' : ''">
                                                <td class="p-2 text-center border-r border-gray-100">
                                                    <input type="checkbox" 
                                                           :checked="selectedIds.includes(item.id)"
                                                           @click.stop="toggleSelection(item)"
                                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3 h-3">
                                                </td>
                                                <td class="p-2 px-4 border-r border-gray-100 font-mono" x-text="item.bem.id_bem"></td>
                                                <td class="p-2 px-4 truncate uppercase max-w-[150px]" x-text="item.bem.descricao"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="p-2 bg-gray-300 border-t border-gray-400 flex justify-between items-center text-[10px] font-black text-blue-900 px-4">
                            <span>SISTEMA DE BENS M├ôVEIS</span>
                            <span x-text="filteredPendencias.length + ' Bens Pendentes'"></span>
                        </div>
                    </div>

                    <!-- Direita: A├º├Áes de Tratativa -->
                    <div class="w-full md:w-[35%] bg-[#F8F9FA] p-5 flex flex-col gap-4 relative overflow-y-auto">
                        <div class="flex items-center gap-2 border-b-2 border-gray-200 pb-2">
                            <span class="text-blue-900 font-black text-lg">­ƒÆí</span>
                            <div>
                                <h3 class="font-black text-blue-900 uppercase text-xs">Registro de A├º├Áes</h3>
                                <p class="text-[9px] text-gray-500 font-bold tracking-tight uppercase" x-text="(selectedIds.length > 0 ? selectedIds.length : 'Nenhum') + ' Item(ns) Selecionado(s)'"></p>
                            </div>
                        </div>

                        <!-- Info do Item unico -->
                        <div x-show="selectedIds.length === 1 && selectedItem" class="bg-blue-50 border-l-4 border-[#004A80] p-3 rounded shadow-sm">
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-tighter">Item em Foco</p>
                            <p class="font-mono font-black text-sm text-blue-900 mt-1" x-text="selectedItem?.bem?.id_bem"></p>
                            <p class="text-[10px] font-bold text-gray-700 uppercase mt-1 truncate" x-text="selectedItem?.bem?.descricao"></p>
                            <p class="text-[9px] font-bold text-gray-500 mt-2 uppercase bg-white p-1 rounded border border-blue-100">
                                ­ƒôì <span x-text="selectedItem?.bem?.dependencia_original || 'S/ LOCAL ORIGINAL'"></span>
                            </p>
                        </div>
                        <!-- Info Multiplos Itens -->
                        <div x-show="selectedIds.length > 1" class="bg-amber-50 border-l-4 border-amber-500 p-3 rounded shadow-sm flex items-center gap-3">
                            <span class="text-2xl">ÔÜá´©Å</span>
                            <div>
                                <p class="text-[10px] font-black text-amber-800 uppercase">A├º├úo em Lote Ativada</p>
                                <p class="text-[9px] font-bold text-amber-700 mt-0.5">A mesma tratativa ser├í aplicada a todos os <span x-text="selectedIds.length"></span> itens.</p>
                            </div>
                        </div>

                        <div class="flex-grow flex flex-col gap-3">
                            <div class="bg-white p-4 border-2 border-gray-400 shadow-sm rounded-sm">
                                <p class="text-[9px] font-black text-gray-400 uppercase mb-3 border-b pb-1">Tratativa Selecionada</p>
                                <div class="grid grid-cols-3 gap-y-3 gap-x-2">
                                    @foreach([
                                        'imprimir' => 'Imprimir', 
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

                            <textarea x-model="observacao" rows="3" 
                                      class="w-full border-gray-300 rounded shadow-inner text-[11px] font-bold uppercase p-3 focus:ring-[#004A80] focus:border-[#004A80] placeholder:opacity-40" 
                                      placeholder="Digite aqui as tratativas realizadas (Ex: ITEM LOCALIZADO NO FUNDO B├ìBLICO)..."></textarea>

                            <!-- New Asset Fields (Conditional) -->
                            <div x-show="tratativa === 'novo'" x-transition class="bg-amber-50 border-2 border-amber-300 p-4 rounded-sm space-y-3 shadow-md relative overflow-hidden">
                                <div class="absolute inset-0 bg-white/40 pointer-events-none"></div>
                                <div class="relative z-10 flex justify-between items-center border-b border-amber-200 pb-2">
                                    <p class="text-[10px] font-black text-amber-700 uppercase">­ƒôä Registro de Novo Bem</p>
                                    <span x-show="false" class="bg-amber-400 text-amber-900 text-[8px] px-2 py-0.5 rounded shadow-sm font-black uppercase inline-flex items-center gap-1 animate-pulse"><span class="block w-1.5 h-1.5 rounded-full bg-red-600"></span> SEM ETIQUETA / OFFLINE</span>
                                </div>
                                <div class="relative z-10 space-y-1 mt-2">
                                    <label class="text-[10px] font-black text-gray-500 uppercase">Descri├º├úo do Item</label>
                                    <input type="text" x-model="novaDescricao" class="w-full p-2.5 text-[11px] border border-gray-400 rounded shadow-inner font-bold uppercase focus:ring-1 focus:ring-amber-400 focus:border-amber-400" placeholder="EX: VENTILADOR DE PAREDE PRETO">
                                </div>
                                <div class="relative z-10 space-y-1">
                                    <label class="text-[10px] font-black text-gray-500 uppercase">Depend├¬ncia Destino</label>
                                    <select x-model="novaDependencia" class="w-full p-2.5 text-[11px] border border-gray-400 rounded shadow-inner font-bold focus:ring-1 focus:ring-amber-400 focus:border-amber-400">
                                        <option value="">Selecione a depend├¬ncia...</option>
                                        @foreach(App\Models\Dependencia::orderBy('nome')->get() as $dep)
                                            <option value="{{ $dep->id }}">{{ $dep->id }} - {{ $dep->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="relative z-10 flex items-center gap-2 mt-3 p-2 bg-blue-50/80 border border-blue-200 rounded">
                                    <input type="checkbox" x-model="isDoacao" id="isDoacao" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="isDoacao" class="text-[10px] font-black text-blue-700 uppercase cursor-pointer select-none">
                                        ­ƒôä Este item ├® uma doa├º├úo? (Gera Formul├írios 14.1 e 14.2)
                                    </label>
                                </div>
                            </div>

                            <div class="flex gap-4 mt-auto">
                                <button @click="tratativa = 'novo'; resetSelectionForNew()" class="w-1/4 bg-amber-100 hover:bg-amber-200 border border-amber-300 text-amber-800 font-black text-[9px] uppercase shadow-sm transition p-2 flex flex-col items-center justify-center text-center leading-tight">
                                    <span>Ô×ò ADD BEM</span>
                                    <span>S/ ETIQUETA</span>
                                </button>
                                <button @click="showPendencias = false" class="w-1/4 bg-gray-200 border border-gray-400 p-2 font-black text-gray-600 text-[10px] shadow hover:bg-white transition uppercase">Fechar</button>
                                <button @click="saveTratativa()" 
                                        :disabled="!isValidToSave()"
                                        class="w-2/4 bg-[#004A80] text-white p-3 rounded font-black text-sm shadow-lg hover:bg-[#003B66] hover:scale-[1.02] transition flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="!savingTratativa">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs group-hover:rotate-12 transition group-disabled:hidden">­ƒÆ¥</span>
                                            <span x-text="getSaveButtonText()"></span>
                                        </div>
                                    </template>
                                    <template x-if="savingTratativa">
                                        <div class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-[11px]">PROCESSANDO...</span>
                                        </div>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                exigirDependencia: true, // Toggle local Dependency Requirement
                buscaRapida: '',
                showBuscaResultados: false,
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
                showScannerManual: false,
                showFeedback: false,
                feedbackTitle: '',
                feedbackMessage: '',
                feedbackIcon: '',
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

                get resultadosBuscaRapida() {
                    const searchLower = this.buscaRapida.toLowerCase();
                    if (!searchLower || searchLower.length < 2) return [];

                    // Return max 10 results from local allPendencias that match
                    return this.allPendencias.filter(item => {
                        return (item.status_leitura === 'nao_encontrado' || item.status_leitura === 'novo_sistema') &&
                               // Not treated
                               (!item.tratativa || item.tratativa === 'nenhuma') &&
                               (item.id_bem.toLowerCase().includes(searchLower) || 
                                (item.bem && item.bem.descricao && item.bem.descricao.toLowerCase().includes(searchLower)));
                    }).slice(0, 10);
                },

                selecionarBuscaRapida(item) {
                    this.showBuscaResultados = false;
                    this.buscaRapida = '';
                    this.barcode = item.id_bem;
                    this.processScan();
                },

                isNewItemOflfine() {
                    return this.selectedIds.length === 0 && this.tratativa === 'novo';
                },

                isValidToSave() {
                    if (this.savingTratativa) return false;
                    
                    // Offline addition mode (No Tag)
                    if (this.selectedIds.length === 0) {
                        return this.tratativa === 'novo' && 
                               this.novaDescricao.trim() !== '' && 
                               this.novaDependencia !== '';
                    }
                    
                    // Normal item mode
                    return this.tratativa && this.tratativa !== '';
                },

                getSaveButtonText() {
                    if (this.selectedIds.length === 0 && this.tratativa !== 'novo') {
                        return 'SELECIONE UM ITEM NA LISTA';
                    }
                    if (this.isNewItemOflfine()) {
                        return 'SALVAR NOVO BEM SEM ETIQUETA';
                    }
                    return this.selectedIds.length > 1 ? 'SALVAR ' + this.selectedIds.length + ' ITENS' : 'SALVAR TRATATIVA';
                },

                resetSelectionForNew() {
                    this.selectedIds = [];
                    this.selectedItem = null;
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

                isValidToSave() {
                    if (this.selectedIds.length === 0) return false;
                    if (!this.tratativa) return false;
                    
                    if (this.tratativa === 'novo') {
                        if (!this.novaDescricao || this.novaDescricao.length < 3) return false;
                        if (this.exigirDependencia && !this.novaDependencia) return false;
                    }
                    
                    if (this.tratativa === 'transferir') {
                        if (!this.novaDependencia) return false;
                    }

                    return true;
                },

                getSaveButtonText() {
                    const count = this.selectedIds.length;
                    return count > 1 ? `APLICAR AOS ${count} ITENS` : `SALVAR MUDAN├çAS`;
                },

                async saveTratativa() {
                    if (!this.isValidToSave()) return;

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

                            // Clear fields (removed auto-hide to allow bulk adding)
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
                    if (!this.autoFocus || this.cameraActive || this.showPendencias) return; 
                    
                    if (this.showScannerManual) {
                        this.$nextTick(() => {
                            const overlayInput = document.getElementById('scannerInputManualOverlay');
                            if (overlayInput && document.activeElement !== overlayInput) overlayInput.focus();
                        });
                        return;
                    }

                    this.$nextTick(() => {
                        const input = document.getElementById('scannerInput');
                        if (input && document.activeElement !== input) input.focus();
                    });
                },

                toggleCamera() {
                    if (this.cameraActive) {
                        this.stopCamera();
                    } else {
                        // Ensure overlay logic
                        this.showScannerManual = false; 
                        this.startCamera();
                    }
                },

                abrirScannerManual() {
                    this.stopCamera(); // Auto-stop camera if running
                    this.showScannerManual = true;
                    // Retain visual autofocus capability
                    setTimeout(() => {
                        const overlayInput = document.getElementById('scannerInputManualOverlay');
                        if(overlayInput) overlayInput.focus();
                    }, 200);
                },

                fecharScannerManual() {
                    this.showScannerManual = false;
                    this.barcode = '';
                    // Reset focus logic if returning to main
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
                    if (this.exigirDependencia && !this.dependenciaId) {
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

                            if (data.status === 'error') {
                                playError();
                                this.showToastOverlay('Erro', data.message, 'ÔØî');
                            } else {
                                playSuccess();
                                this.showToastOverlay(data.status === 'success' ? 'Sucesso' : 'Aviso', data.message, data.status === 'success' ? 'Ô£à' : 'ÔÜá´©Å');
                            }
                            
                            this.barcode = '';
                        }
                    } catch (error) {
                        console.error('Erro no process', error);
                        Toast.fire({ icon: 'error', title: 'Erro de comunica├º├úo.' });
                        this.showToastOverlay('Erro', 'Falha ao comunicar porta.', 'ÔØî');
                    } finally {
                        this.loading = false;
                        this.focusScanner();
                    }
                },

                showToastOverlay(title, msg, icon) {
                    if(!this.showScannerManual) return;
                    this.feedbackTitle = title;
                    this.feedbackMessage = msg;
                    this.feedbackIcon = icon;
                    this.showFeedback = true;
                    setTimeout(() => {
                        this.showFeedback = false;
                    }, 2500);
                }
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
