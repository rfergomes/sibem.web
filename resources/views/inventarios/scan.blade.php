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
            gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + duration / 1000);
            oscillator.connect(gain);
            gain.connect(audioCtx.destination);
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + duration / 1000);
        };

        window.playSuccess = () => beep(880, 150, 'sine', 0.2);
        window.playError = () => {
            beep(220, 100, 'square', 0.1);
            setTimeout(() => beep(110, 200, 'square', 0.1), 120);
        };
    </script>
@endpush

@section('title', 'Leitor de Inventário')

@section('content')
    <!-- Main Container: Uses flex-1 and overflow-hidden to fit between header/footer -->
    <div x-data="inventarioScanner()" @click="focusScanner()"
        class="flex flex-col lg:flex-row gap-5 h-auto lg:h-[calc(100vh-135px)] overflow-y-auto lg:overflow-hidden p-2">

        <!-- SLIM SIDEBAR: Stats & Actions -->
        <div class="lg:w-64 flex flex-col h-full gap-4 pr-1">
            
            <div class="flex-grow flex flex-col gap-4 overflow-y-auto custom-scrollbar">
                <!-- Header Card (Compact) -->
                <div
                    class="bg-gradient-to-br from-[#004A80] to-[#003B66] text-white p-4 rounded-xl shadow-lg border border-blue-400/20">
                    <p class="text-[9px] font-black uppercase opacity-60 tracking-[0.2em] mb-1">Localidade</p>
                <p class="text-sm font-black leading-tight">{{ $inventario->igreja->nome ?? 'LOCALIDADE NÃO DEFINIDA' }}</p>
                    <div
                        class="mt-3 pt-3 border-t border-white/10 flex justify-between items-center text-[10px] font-bold opacity-70">
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
            </div>

            <!-- Sidebar Actions (Fixed at bottom) -->
            <div class="shrink-0 space-y-2 pt-2 border-t border-gray-200 mt-2">
                <button onclick="location.reload()"
                    class="w-full py-2 bg-gray-50 hover:bg-gray-100 text-gray-500 rounded-lg text-[9px] font-black uppercase tracking-widest transition border border-gray-200">
                        🔄 Sincronizar Tudo
                    </button>
                    <form id="finalizeForm" action="{{ route('inventarios.finalize', $inventario->id) }}" method="POST">
                        @csrf
                        <button type="button" @click="confirmFinalize()"
                            class="w-full py-3 bg-[#004A80] hover:bg-[#00355B] text-white rounded-xl font-black text-xs shadow-lg shadow-blue-900/20 transition uppercase tracking-widest flex items-center justify-center gap-2">
                            🏁 Finalizar
                        </button>
                    </form>
                </div>
            </div>

            <!-- OLD MAIN AREA REMOVED - REPLACED BY PENDÊNCIAS NATIVE VIEW -->

            <!-- SCANNER MANUAL FULLSCREEN OVERLAY -->
            <div x-show="showScannerManual" class="fixed inset-0 z-[140] bg-[#F5F7FA] flex flex-col"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-full"
                x-cloak>

                <!-- Header bar -->
                <div
                    class="bg-gray-100 text-[#004A80] p-4 flex justify-between items-center shadow-sm border-b-2 border-gray-200 z-10 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500 animate-pulse text-2xl">📠</span>
                        <div class="flex flex-col">
                            <span class="font-black text-sm md:text-lg uppercase tracking-widest text-[#004A80]">Leitor /
                                Digitação</span>
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Aguardando Código de
                                Barras...</span>
                        </div>
                    </div>
                    <!-- Global Feedback Toast Overlay -->
                    <div x-show="showFeedback"
                        class="fixed top-20 left-1/2 transform -translate-x-1/2 z-[200] w-[90%] md:w-auto"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-8"
                        x-cloak>
                        <div
                            class="bg-gray-900/90 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 border-l-4 border-blue-500 backdrop-blur-md">
                            <div class="text-3xl" x-text="feedbackIcon"></div>
                            <div class="flex flex-col">
                                <span class="font-black text-[13px] uppercase tracking-wider text-gray-100"
                                    x-text="feedbackTitle"></span>
                                <span class="text-[11px] font-bold text-gray-300 mt-0.5" x-text="feedbackMessage"></span>
                            </div>
                        </div>
                    </div>
                    <button @click="fecharScannerManual()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-black px-6 py-3 rounded-lg text-[10px] md:text-sm uppercase shadow-sm flex items-center gap-2 transition">
                        <span>X</span> Voltar
                    </button>
                </div>

                <div class="flex-grow p-4 md:p-8 flex flex-col gap-4 overflow-y-auto">
                    <!-- CONTROLS & SETTINGS (Moved from Home) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 shrink-0">
                        <!-- Location Selector -->
                        <div class="bg-white p-3 border border-gray-200 shadow-sm rounded-xl">
                            <span class="text-[9px] font-black uppercase text-gray-500 mb-1 block">Localização Base</span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs opacity-40">📍</span>
                                <select x-model="dependenciaId"
                                    class="w-full text-xs font-bold border-gray-200 rounded-lg focus:ring-0 focus:border-blue-600 uppercase tracking-tight py-1.5 shadow-inner bg-gray-50">
                                    <option value="">DEFINA LOCALIZAÇÃO FÍSICA AQUI...</option>
                                    @foreach(App\Models\Dependencia::orderBy('nome')->get() as $dep)
                                        <option value="{{ $dep->id }}">{{ str_pad($dep->id, 3, '0', STR_PAD_LEFT) }} -
                                            {{ $dep->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Search and Switches -->
                        <div class="flex flex-col gap-2">
                            <!-- Busca Rápida Toolbar -->
                            <div
                                class="relative bg-white border border-gray-200 rounded-xl shadow-sm flex items-center flex-grow p-1">
                                <span class="pl-2 pr-1 text-xs opacity-40">🔍</span>
                                <input type="text" x-model="buscaRapida" @focus="showBuscaResultados = true"
                                    @click.away="showBuscaResultados = false" placeholder="BUSCA RÁPIDA (NOME OU CÓDIGO)..."
                                    class="w-full bg-transparent border-none text-[10px] uppercase font-bold focus:ring-0 py-2">

                                <!-- Dropdown Busca -->
                                <div x-show="showBuscaResultados && buscaRapida.length > 0"
                                    class="absolute top-12 left-0 z-50 w-full bg-white border-2 border-[#004A80] rounded-xl shadow-2xl max-h-60 overflow-y-auto">
                                    <template x-if="resultadosBuscaRapida.length === 0">
                                        <div class="p-4 text-center text-[10px] font-black text-gray-400">NENHUM ENCONTRADO...
                                        </div>
                                    </template>
                                    <template x-for="item in resultadosBuscaRapida" :key="item.id_bem">
                                        <div @click="selecionarBuscaRapida(item)"
                                            class="p-3 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition">
                                            <div class="text-[11px] font-black text-gray-800 uppercase truncate"
                                                x-text="item.bem.descricao"></div>
                                            <div class="text-[9px] font-mono font-bold text-[#004A80]"
                                                x-text="item.id_bem + ' • ' + (item.bem.dependencia_original || '')"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Tiny Switches row -->
                            <div class="flex items-center gap-4 px-1 py-1">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <span
                                        class="text-[9px] font-black uppercase tracking-tighter text-gray-500 group-hover:text-blue-700">Travar
                                        Local</span>
                                    <div class="relative inline-flex items-center scale-90">
                                        <input type="checkbox" x-model="exigirDependencia" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004A80]">
                                        </div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <span
                                        class="text-[9px] font-black uppercase tracking-tighter text-gray-500 group-hover:text-blue-700">Auto-Bipe</span>
                                    <div class="relative inline-flex items-center scale-90">
                                        <input type="checkbox" x-model="autoFocus" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004A80]">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Main Input Field -->
                    <div
                        class="relative bg-white border-4 border-gray-200 rounded-3xl shadow-xl p-2 shrink-0 flex items-center justify-center group overflow-hidden focus-within:border-[#004A80] focus-within:ring-4 focus-within:ring-[#004A80]/20 transition-all duration-300 min-h-[140px] md:min-h-[180px]">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-focus-within:opacity-100 transition duration-500 pointer-events-none">
                        </div>
                        <input type="text" x-model="barcode" @keyup.enter="processScan()" maxlength="12" placeholder="000000000"
                            class="w-full text-5xl md:text-8xl font-black font-mono tracking-tighter text-gray-900 border-none focus:ring-0 text-center uppercase p-6 bg-transparent relative z-10"
                            id="scannerInputManualOverlay"
                            @input="if(barcode.length >= 1) { String(barcode).length === 12 ? processScan() : null; }"
                            @blur="if(showScannerManual && autoFocus) setTimeout(() => document.getElementById('scannerInputManualOverlay')?.focus(), 150)">

                        <div class="absolute bottom-4 left-0 right-0 text-center pointer-events-none">
                            <p class="text-[10px] md:text-[14px] font-black tracking-[0.3em]"
                                :class="barcode.length > 0 ? 'text-[#004A80] animate-none' : 'text-gray-300 animate-pulse'"
                                x-text="barcode.length > 0 ? 'Pressione ENTER ou bip novamente...' : 'Aguardando Leitura...'">
                            </p>
                        </div>
                    </div>

                    <!-- Last 3 items read feedback -->
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 flex-grow flex flex-col min-h-[250px] shrink-0">
                        <div
                            class="bg-gray-50 border-b border-gray-200 text-gray-500 p-3 px-6 flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-widest">Acompanhamento Log (Últimos
                                itens)</span>
                            <span class="bg-blue-100/50 text-blue-800 px-2 py-0.5 rounded text-[10px] font-black"
                                x-text="history.length + ' LIDOS HOJE'"></span>
                        </div>
                        <div class="overflow-y-auto custom-scrollbar flex-grow p-2">
                            <div class="space-y-2">
                                <template x-if="history.length === 0">
                                    <div
                                        class="h-32 flex items-center justify-center p-6 text-center text-gray-300 font-black text-sm uppercase tracking-widest border-2 border-dashed border-gray-100 rounded-xl m-4">
                                        Nenhuma leitura registrada nesta sessão...
                                    </div>
                                </template>
                                <template x-for="(item, index) in history.slice(0, 4)" :key="index + '-' + item.barcode">
                                    <div class="bg-white border-2 border-gray-100 shadow-sm p-4 rounded-xl flex items-center justify-between animate-fade-in hover:border-gray-300 transition"
                                        :class="index === 0 ? 'ring-2 ring-blue-500/30 bg-blue-50/20' : ''">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center shrink-0">
                                                <span class="text-xl"
                                                    :class="item.lido ? 'text-green-500' : 'text-gray-400 opacity-50'">✔️</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-lg md:text-xl font-black font-mono tracking-tighter"
                                                    :class="item.is_cross_church ? 'text-red-700' : 'text-[#004A80]'"
                                                    x-text="item.barcode"></span>
                                                <span
                                                    class="text-[11px] md:text-xs font-bold text-gray-500 uppercase tracking-wide truncate max-w-[200px] md:max-w-md"
                                                    x-text="item.descricao"></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span
                                                class="px-3 py-1.5 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-widest text-center"
                                                :class="item.situacao.includes('DIVERGÊNCIA') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                                x-text="item.situacao"></span>
                                            <span
                                                class="text-[9px] font-black text-gray-400 mt-2 tracking-widest uppercase hidden md:block"
                                                x-text="item.dependencia ? 'LOC: ' + item.dependencia : ''"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMERA FULLSCREEN OVERLAY -->
            <div x-show="cameraActive" class="fixed inset-0 z-[150] bg-black flex flex-col"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak>
                <!-- Header bar for Camera -->
                <div class="bg-gray-900 text-white p-3 flex justify-between items-center shadow-md z-10">
                    <div class="flex items-center gap-2">
                        <span class="text-green-500 animate-pulse">📷</span>
                        <span class="font-black text-sm uppercase tracking-widest text-gray-200">Scanner Ativo</span>
                    </div>
                    <button @click="stopCamera()"
                        class="bg-red-600 hover:bg-red-700 text-white font-black px-4 py-2 rounded-lg text-xs uppercase shadow-lg flex items-center gap-2 transition">
                        <span>⏹️</span> Encerrar
                    </button>
                </div>

                <!-- Camera Viewfinder -->
                <div class="flex-grow relative overflow-hidden flex items-center justify-center">
                    <div id="reader"
                        class="w-full h-full object-cover [&>video]:object-cover [&>video]:w-full [&>video]:h-full"></div>
                    <div
                        class="absolute inset-0 pointer-events-none border-[3px] border-dashed border-white/40 m-6 sm:m-12 rounded-xl flex items-center justify-center shadow-[0_0_0_9999px_rgba(0,0,0,0.5)]">
                        <div
                            class="w-3/4 h-0.5 bg-red-500/80 absolute animate-[pulse_2s_ease-in-out_infinite] shadow-[0_0_10px_rgba(239,68,68,0.8)]">
                        </div>
                    </div>

                    <div class="absolute bottom-10 left-0 right-0 text-center pointer-events-none drop-shadow-md">
                        <p
                            class="text-white text-xs font-black uppercase tracking-widest bg-black/50 inline-block px-4 py-2 rounded-full backdrop-blur-sm">
                            Centralize o código de barras
                        </p>
                    </div>
                </div>
            </div>


            <!-- CONSULTAS DASHBOARD OVERVIEW -->
            <div class="flex-grow flex flex-col overflow-hidden bg-[#F5F7FA] rounded-xl relative shadow-md animate-fade-in border border-blue-900/20"
                x-show="!showScannerManual && !cameraActive">
                <!-- Header Toolbar -->
                <div class="bg-gray-100 border-b border-gray-200 p-3 flex justify-between items-center z-10 shrink-0">
                    <div class="flex items-center gap-2 md:gap-4">
                        <div class="flex flex-col">
                            <label class="text-[9px] font-black uppercase text-gray-500 mb-0.5 ml-1">Pesquisar</label>
                            <div class="relative">
                                <input type="text" x-model="consultaPesquisa" placeholder="Digite aqui..."
                                    class="text-[11px] border-gray-300 rounded shadow-inner py-1.5 w-32 md:w-48 uppercase font-bold focus:ring-[#004A80] focus:border-[#004A80]">
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label class="text-[9px] font-black uppercase text-gray-500 mb-0.5 ml-1">Selecionar Consulta</label>
                            <select x-model="consultaAtiva"
                                class="text-[11px] border-gray-300 rounded shadow-inner py-1.5 w-40 md:w-56 uppercase font-black focus:ring-[#004A80] focus:border-[#004A80] text-[#004A80]">
                                <option value="lista_geral">BENS LIDOS (HISTÓRICO)</option>
                                <option value="bens_totalizados">BENS TOTALIZADOS</option>
                                <option value="bens_localizados">BENS LOCALIZADOS</option>
                                <option value="bens_pendentes">BENS PENDENTES / NÃO LIDOS</option>
                                <option value="lidos_repetidos">LIDOS REPETIDOS / DIVERGÊNCIA</option>
                                <option value="dependencias">DEPENDÊNCIAS DA TAREFA</option>
                            </select>
                        </div>
                        <button class="bg-gray-200 text-[#004A80] p-1.5 mt-4 rounded shadow-sm hover:bg-gray-300 transition"
                            @click="consultaPesquisa = ''" title="Limpar Pesquisa">
                            <span class="text-lg font-black leading-none">↺</span>
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-2 items-center justify-end mt-4 md:mt-0">
                        <button @click="toggleCamera()"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold px-2 py-1.5 md:px-3 text-[10px] sm:text-[11px] rounded uppercase shadow-sm flex items-center gap-1.5 transition group flex-1 md:flex-none justify-center tracking-wider">
                            <span class="text-base sm:text-lg group-hover:scale-110 transition">📷</span>
                            <span>CÂMERA</span>
                        </button>
                        <button @click="abrirScannerManual()"
                            class="bg-[#004A80] hover:bg-[#003B66] text-white font-bold px-2 py-1.5 md:px-3 text-[10px] sm:text-[11px] rounded uppercase shadow-sm flex items-center gap-1.5 transition group flex-1 md:flex-none justify-center relative overflow-hidden tracking-wider">
                            <div class="absolute inset-0 bg-white/5 group-hover:bg-transparent transition object-cover"></div>
                            <span
                                class="text-base sm:text-lg group-hover:scale-110 transition animate-pulse relative z-10">📠</span>
                            <span class="relative z-10">LEITOR</span>
                        </button>
                        <button @click="showPendencias = true"
                            class="bg-white border-2 border-red-700 hover:bg-red-50 text-red-700 font-bold px-2 py-1.5 md:px-3 text-[10px] sm:text-[11px] rounded uppercase shadow-sm flex items-center gap-1.5 transition group flex-1 sm:flex-none justify-center tracking-wider">
                            <span class="text-base sm:text-lg group-hover:scale-110 transition">📋</span>
                            <span>PENDÊNCIAS</span>
                        </button>
                    </div>
                </div>

                <!-- Content Area (Tables) -->
                <div class="flex-grow overflow-hidden flex flex-col bg-white">
                    <div class="bg-gradient-to-r from-[#004A80] to-[#003B66] text-white text-center text-[10px] sm:text-xs font-black py-1.5 border-b border-blue-900 uppercase tracking-[0.2em] shadow-sm shrink-0"
                        x-text="getConsultaTitulo()"></div>

                    <div class="overflow-y-auto flex-grow custom-scrollbar relative">
                        <!-- 1. LISTA GERAL -->
                        <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'lista_geral'">
                            <thead
                                class="bg-blue-50/90 text-gray-500 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm">
                                <tr>
                                    <th class="p-2 border-r border-[#004A80]/10 w-32 font-black pl-4">Etiqueta</th>
                                    <th class="p-2 border-r border-[#004A80]/10 font-black pl-4">Bem Móvel</th>
                                    <th class="p-2 border-r border-[#004A80]/10 w-48 hidden md:table-cell font-black pl-4">
                                        Dependência</th>
                                    <th class="p-2 border-r border-[#004A80]/10 w-32 font-black pl-4 hidden sm:table-cell">
                                        Situação</th>
                                    <th class="p-2 w-16 text-center font-black">Lido</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, idx) in filtradosListaGeral" :key="idx + '-' + item.barcode">
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="p-2 border-r border-gray-100 font-mono font-bold pl-4"
                                            :class="item.is_cross_church ? 'text-red-700 font-black' : 'text-[#004A80]'"
                                            x-text="item.barcode"></td>
                                        <td class="p-2 border-r border-gray-100 uppercase" x-text="item.descricao"></td>
                                        <td class="p-2 border-r border-gray-100 uppercase font-bold text-gray-600 hidden md:table-cell"
                                            x-text="getDependenciaNome(item.dependencia)"></td>
                                        <td class="p-2 border-r border-gray-100 hidden sm:table-cell">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest"
                                                :class="item.situacao.includes('DIVERGÊNCIA') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                                x-text="item.situacao"></span>
                                        </td>
                                        <td class="p-2 text-center text-lg"><span x-show="item.lido">✔️</span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- 2. BENS TOTALIZADOS -->
                        <table class="w-full text-left border-collapse text-[11px]"
                            x-show="consultaAtiva === 'bens_totalizados'" x-cloak>
                            <thead
                                class="bg-blue-50/90 text-gray-500 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm">
                                <tr>
                                    <th class="p-2 border-r border-[#004A80]/10 font-black pl-4">Bem Móvel</th>
                                    <th class="p-2 border-r border-[#004A80]/10 w-64 font-black pl-4">Dependência</th>
                                    <th class="p-2 w-24 text-center font-black">Qtde</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(item, idx) in filtradosBensTotalizados" :key="idx + '-' + item.nome">
                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="p-2 border-r border-gray-100 uppercase font-bold pl-4" x-text="item.nome">
                                        </td>
                                        <td class="p-2 border-r border-gray-100 uppercase font-black text-gray-600 pl-4"
                                            x-text="item.dependencia"></td>
                                        <td class="p-2 text-center font-black text-[#004A80] bg-blue-50/30 table-cell"
                                            x-text="item.qtde"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- 3. BENS LOCALIZADOS -->
                        <table class="w-full text-left border-collapse text-[11px]"
                            x-show="consultaAtiva === 'bens_localizados'" x-cloak>
                            <thead
                                class="bg-green-50/90 text-green-700 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm border-b border-green-200">
                                <tr>
                                    <th class="p-2 border-r border-green-200 w-32 font-black pl-4">Etiqueta</th>
                                    <th class="p-2 border-r border-green-200 font-black pl-4">Bem Móvel</th>
                                    <th class="p-2 border-r border-green-200 w-48 font-black pl-4 hidden sm:table-cell">
                                        Dependência Original</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="item in filtradosBensLocalizados" :key="item.id_bem">
                                    <tr
                                        class="hover:bg-green-50 transition border-l-2 border-transparent hover:border-green-500">
                                        <td class="p-2 border-r border-gray-100 font-mono font-black text-[#004A80] pl-4"
                                            x-text="item.id_bem"></td>
                                        <td class="p-2 border-r border-gray-100 uppercase" x-text="item.descricao"></td>
                                        <td class="p-2 border-r border-gray-100 uppercase font-bold text-gray-600 hidden sm:table-cell pl-4"
                                            x-text="getDependenciaNome(item.dependencia)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- 4. BENS PENDENTES (Grouped by Dependencia) -->
                        <div x-show="consultaAtiva === 'bens_pendentes'" x-cloak>
                            <template x-for="(grupo, idx) in filtradosBensPendentes" :key="idx + '-' + grupo.dependencia">
                                <div class="mb-4">
                                    <div
                                        class="bg-[#F0F4F8] px-4 py-2 flex justify-between border-y border-[#004A80]/20 sticky top-0 z-10 shadow-sm backdrop-blur-sm">
                                        <span
                                            class="text-[10px] md:text-[11px] font-black text-[#004A80] tracking-widest uppercase">---
                                            <span x-text="getDependenciaNome(grupo.dependencia)"></span> ---</span>
                                        <span class="text-[10px] font-black text-red-600 uppercase"
                                            x-text="grupo.itens.length + ' PENDENTES'"></span>
                                    </div>
                                    <table class="w-full text-left border-collapse text-[11px]">
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="item in grupo.itens" :key="item.id_bem">
                                                <tr class="hover:bg-red-50 transition pl-4 border-l-2 border-transparent hover:border-red-500 cursor-pointer"
                                                    @click="openPendencia(item.id_bem)">
                                                    <td class="p-2 border-r border-gray-100 font-mono text-red-800 w-32 pl-6 font-bold hover:underline"
                                                        x-text="item.id_bem" title="Clique para abrir Pendências"></td>
                                                    <td class="p-2 border-r border-gray-100 uppercase text-gray-700 pl-4"
                                                        x-text="item.descricao"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </template>
                        </div>

                        <!-- 4.B LIDOS REPETIDOS / DIVERGENCIA -->
                        <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'lidos_repetidos'"
                            x-cloak>
                            <thead
                                class="bg-amber-50/90 text-amber-800 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm border-b border-amber-200">
                                <tr>
                                    <th class="p-2 border-r border-amber-200/50 w-32 font-black pl-4">Etiqueta</th>
                                    <th class="p-2 border-r border-amber-200/50 font-black pl-4">Bem Móvel / Aviso</th>
                                    <th class="p-2 border-r border-amber-200/50 w-48 font-black pl-4 hidden sm:table-cell">
                                        Dependência Lida</th>
                                    <th class="p-2 border-r border-amber-200/50 w-32 font-black pl-4 hidden md:table-cell">
                                        Situação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100/50">
                                <template x-for="item in filtradosLidosRepetidos" :key="item.barcode">
                                    <tr class="hover:bg-amber-50/80 transition bg-amber-50/30 border-l-2 border-amber-400">
                                        <td class="p-2 border-r border-amber-100/50 font-mono font-black text-amber-900 pl-4"
                                            x-text="item.barcode"></td>
                                        <td class="p-2 border-r border-amber-100/50 uppercase pl-4">
                                            <span x-text="item.descricao" class="font-bold text-gray-800"></span>
                                            <div x-show="item.aviso"
                                                class="text-[9px] text-red-600 mt-1 uppercase font-black tracking-tighter"
                                                x-text="item.aviso"></div>
                                        </td>
                                        <td class="p-2 border-r border-amber-100/50 uppercase font-bold text-amber-700 hidden sm:table-cell pl-4"
                                            x-text="getDependenciaNome(item.dependencia)"></td>
                                        <td class="p-2 border-r border-amber-100/50 hidden md:table-cell pl-4">
                                            <span
                                                class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-700"
                                                x-text="item.situacao"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- 5. DEPENDÊNCIAS -->
                        <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'dependencias'"
                            x-cloak>
                            <thead
                                class="bg-blue-50/90 text-gray-500 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm">
                                <tr>
                                    <th class="p-2 border-r border-[#004A80]/10 w-24 font-black text-center">Código</th>
                                    <th class="p-2 border-r border-[#004A80]/10 font-black pl-4">Dependência</th>
                                    <th class="p-2 border-r border-[#004A80]/10 w-24 text-center font-black">Conf. / Tratado
                                    </th>
                                    <th class="p-2 w-24 text-center font-black">Pendentes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="dep in filtradosDependencias" :key="dep.codigo">
                                    <tr class="hover:bg-blue-50 transition border-l-4 border-transparent hover:border-[#004A80] cursor-pointer"
                                        @click="filtrarPorDependencia(dep.codigo)"
                                        title="Clique para filtrar pendentes nesta dependência">
                                        <td class="p-2 border-r border-gray-100 font-mono text-gray-500 font-bold text-center bg-gray-50/50"
                                            x-text="dep.codigo"></td>
                                        <td class="p-2 border-r border-gray-100 uppercase font-bold pl-4 text-blue-900"
                                            x-text="dep.nome"></td>
                                        <td class="p-2 text-center text-green-700 font-black border-r border-gray-100 bg-green-50/30"
                                            x-text="dep.localizados"></td>
                                        <td class="p-2 text-center text-red-600 font-black bg-red-50/30" x-text="dep.pendentes">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Summary (Optional) -->
                    <div
                        class="bg-gray-100 p-1.5 border-t border-gray-300 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest shadow-inner relative z-10 flex justify-between px-4">
                        <span>SISTEMA DE BENS MÓVEIS</span>
                        <span x-show="consultaAtiva === 'lista_geral'" x-text="filtradosListaGeral.length + ' Itens'"></span>
                        <span x-show="consultaAtiva === 'bens_totalizados'"
                            x-text="filtradosBensTotalizados.length + ' Grupos'"></span>
                        <span x-show="consultaAtiva === 'bens_localizados'"
                            x-text="filtradosBensLocalizados.length + ' Itens'"></span>
                        <!-- bens pendentes length would be group count, omit or keep empty -->
                        <span x-show="consultaAtiva === 'lidos_repetidos'"
                            x-text="filtradosLidosRepetidos.length + ' Itens'"></span>
                        <span x-show="consultaAtiva === 'dependencias'"
                            x-text="filtradosDependencias.length + ' Locais'"></span>
                    </div>
                </div>
            </div> <!-- FIM DO DASHBOARD OVERVIEW -->
                <template x-teleport="body">
                    <div x-show="showPendencias"
                        class="fixed inset-0 bg-black bg-opacity-60 z-[100] flex items-center justify-center p-4 backdrop-blur-sm"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-cloak>
                        <div
                            class="bg-gray-100 w-full h-full flex flex-col overflow-hidden fixed inset-0 z-[100]">
                            <!-- Title Bar -->
                            <div
                                class="bg-[#003B66] text-white p-3 px-6 shadow-md flex justify-between items-center shrink-0">
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl drop-shadow">📋</span>
                                    <span class="text-lg font-black uppercase tracking-widest text-white/90">Pendências de
                                        Inventário</span>
                                </div>
                                <button @click="showPendencias = false"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold p-1 px-3 rounded shadow-inner text-[10px]">X</button>
                            </div>

                            <!-- Top Stats Row (Full Width) -->
                            <div class="bg-gray-200 border-b border-gray-400 p-2 flex justify-between gap-1 shrink-0">
                                @foreach(['CADASTRAR', 'IMPRIMIR', 'ALTERAR', 'EXCLUIR'] as $label)
                                    <div class="bg-[#003865] text-white p-1 flex-1 text-center border-2 border-gray-500 rounded-sm shadow-sm transition hover:bg-[#002D4C]">
                                        <p class="text-[8px] font-black leading-none mb-0.5 opacity-80">{{ $label }}</p>
                                        <p class="text-xs font-black drop-shadow-md">{{ $tratativaCounts[strtolower($label)] ?? 0 }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-1 flex-col md:flex-row overflow-hidden min-h-0 bg-white">
                                <!-- Left: List -->
                                <div
                                    class="w-full md:w-1/2 lg:w-[60%] flex flex-col border-b md:border-b-0 border-r border-gray-300 bg-white overflow-hidden min-h-0 shadow-[4px_0_24px_-10px_rgba(0,0,0,0.1)] z-10">
                                    <div
                                        class="p-4 bg-gray-50 flex flex-col gap-3 shrink-0">
                                        <div class="relative w-full">
                                            <input type="text" x-model="searchPendencia"
                                                placeholder="Pesquisar por Código ou Descrição..."
                                                class="w-full text-xs border-2 border-gray-200 rounded-lg shadow-inner pl-10 pr-4 py-3 uppercase focus:border-blue-500 focus:ring-blue-500 font-bold transition-all">
                                            <span class="absolute left-4 top-3.5 opacity-40 text-lg">🔍</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 px-2 border-t pt-3 border-gray-200/60">
                                            @foreach(['pendentes' => 'Pendentes', 'encontrados' => 'Encontrados', 'tratados' => 'Tratados', 'todos' => 'Todos'] as $val => $lbl)
                                                <label
                                                    class="flex items-center gap-2 text-[10px] sm:text-xs font-black uppercase tracking-widest cursor-pointer group">
                                                    <input type="radio" x-model="filterStatus" value="{{ $val }}"
                                                        class="text-[#004A80] focus:ring-[#004A80] border-2 border-gray-300 w-4 h-4 cursor-pointer">
                                                    <span class="text-gray-500 group-hover:text-[#004A80] transition-colors"
                                                          :class="filterStatus === '{{ $val }}' ? 'text-[#004A80] font-black' : ''">{{ $lbl }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex-grow overflow-y-auto">
                                        <table class="w-full text-[11px] text-left border-collapse">
                                            <thead
                                                class="bg-[#004A80] text-white sticky top-0 uppercase tracking-widest text-[9px]">
                                                <tr>
                                                    <th class="p-2 w-8 text-center border-r border-blue-900">
                                                        <input type="checkbox" @change="toggleSelectAll($event.target.checked)"
                                                            :checked="filteredPendencias.length > 0 && selectedIds.length === filteredPendencias.length"
                                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3 h-3">
                                                    </th>
                                                    <th class="p-4 px-6 border-r border-[#003B66]/20 w-40">Etiqueta</th>
                                                    <th class="p-4 px-6">Bem Móvel</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                <template x-for="item in filteredPendencias" :key="item.id">
                                                    <tr @click="toggleSelection(item)"
                                                        class="border-b border-gray-200 cursor-pointer hover:bg-blue-50 transition"
                                                        :class="selectedIds.includes(item.id) ? 'bg-[#C1D8FF] font-black' : ''">
                                                        <td class="p-2 text-center border-r border-gray-200">
                                                            <input type="checkbox" :checked="selectedIds.includes(item.id)"
                                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3 h-3 pointer-events-none">
                                                        </td>
                                                        <td class="p-2 px-4 font-mono text-gray-700 border-r border-gray-200"
                                                            x-text="item.id_bem"></td>
                                                        <td class="p-2 px-4 text-gray-800 uppercase"
                                                            x-text="item.bem ? item.bem.descricao : 'S/ DESCRIÇÃO'"></td>
                                                    </tr>
                                                </template>
                                                <tr x-show="filteredPendencias.length === 0">
                                                    <td colspan="3"
                                                        class="p-8 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                                        Nenhum registro encontrado...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                                <!-- END REPLACED BUG -->
                                <!-- Right: Details & Actions -->
                                <div class="w-full md:w-1/2 lg:w-[40%] p-4 sm:p-8 flex flex-col gap-4 overflow-y-auto bg-gray-100 relative shadow-inner">

                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="bg-white border-2 border-gray-200 shadow-sm rounded-lg p-3 hover:shadow-md transition">
                                                    <p class="text-[9px] font-black text-gray-400 text-center uppercase tracking-widest border-b border-gray-100 pb-2 mb-2">Situação Atual</p>
                                                    <div class="flex items-center justify-center gap-2">
                                                        <span class="w-2 h-2 rounded-full" :class="selectedItem ? (selectedItem.tratativa && selectedItem.tratativa !== 'nenhuma' ? 'bg-green-500' : 'bg-red-500') : 'bg-gray-300'"></span>
                                                        <p class="text-base font-black uppercase text-gray-700" 
                                                           x-text="selectedItem ? (selectedItem.tratativa && selectedItem.tratativa !== 'nenhuma' ? selectedItem.tratativa : selectedItem.status_leitura) : 'AGUARDANDO'"></p>
                                                    </div>
                                                </div>
                                                <div class="bg-white border-2 border-gray-200 shadow-sm rounded-lg p-3 hover:shadow-md transition">
                                                    <p class="text-[9px] font-black text-gray-400 text-center uppercase tracking-widest border-b border-gray-100 pb-2 mb-2"># Etiqueta Fís.</p>
                                                    <div class="flex items-center justify-center gap-2">
                                                        <span class="opacity-40 text-sm">🏷️</span>
                                                        <p class="text-base font-black font-mono text-gray-800" x-text="selectedItem ? selectedItem.bem.id_bem : '0000000000'"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="bg-white border-2 border-gray-200 shadow-sm rounded-lg overflow-hidden group">
                                                <div class="bg-gray-50 p-2 text-center text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-gray-500 border-b border-gray-200">
                                                    Dados do Bem Móvel
                                                </div>
                                                <div class="p-6 text-sm font-black leading-relaxed min-h-[100px] uppercase text-gray-800" 
                                                     :class="!selectedItem ? 'text-gray-400 italic flex items-center justify-center text-center' : ''"
                                                     x-text="selectedItem ? selectedItem.bem.descricao : 'Clique em uma linha na tabela ao lado para visualizar os detalhes deste item e iniciar as tratativas.'"></div>
                                            </div>

                                            <div class="bg-white border-2 border-gray-200 shadow-sm rounded-lg overflow-hidden mt-4">
                                                <div class="bg-gray-50 p-2 text-center text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-gray-500 border-b border-gray-200">
                                                    Observação para o Relatório Opcional
                                                </div>
                                                <textarea x-model="observacao" 
                                                      class="w-full border-none text-xs font-bold p-4 h-24 focus:ring-4 focus:ring-blue-50/50 resize-none transition" 
                                                      :class="selectedItem?.observacao?.includes('LOCALIDADE') ? 'text-red-700 bg-red-50' : ''"
                                                      placeholder="Se precisar adicionar alguma nota, digite aqui as tratativas realizadas (Ex: ITEM LOCALIZADO NO FUNDO BÍBLICO, PERTENCE A OUTRA ADMINISTRAÇÃO)..."></textarea>
                                            </div>

                                            <!-- New Asset Fields (Conditional) -->
                                            <div x-show="tratativa === 'novo'" x-transition class="bg-amber-50 border-2 border-amber-400 p-6 rounded-lg space-y-4 shadow-sm mt-4">
                                                <p class="text-xs font-black text-amber-800 uppercase tracking-widest border-b-2 border-amber-200 pb-2 flex items-center gap-2"><span>🏷️</span> Registro de Novo Bem Extra-ERP</p>
                                                <div class="space-y-1.5 mt-4">
                                                    <label class="text-[10px] font-black text-amber-700 uppercase">Descrição do Item</label>
                                                    <input type="text" x-model="novaDescricao" class="w-full p-3 text-sm border-2 border-amber-300 rounded-md font-bold uppercase focus:ring-amber-500 focus:border-amber-500" placeholder="EX: VENTILADOR DE PAREDE PRETO">
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-black text-amber-700 uppercase">Dependência Destino</label>
                                                    <select x-model="novaDependencia" class="w-full p-3 text-sm border-2 border-amber-300 rounded-md font-bold text-gray-700 focus:ring-amber-500 focus:border-amber-500 bg-white">
                                                        <option value="">Selecione um Setor / Dependência...</option>
                                                        @foreach(App\Models\Dependencia::orderBy('nome')->get() as $dep)
                                                            <option value="{{ $dep->id }}">{{ str_pad($dep->id, 3, '0', STR_PAD_LEFT) }} - {{ $dep->nome }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex items-center gap-3 mt-4 p-4 bg-white border border-amber-200 rounded-md shadow-sm">
                                                    <input type="checkbox" x-model="isDoacao" id="isDoacao" class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500 cursor-pointer">
                                                    <label for="isDoacao" class="text-xs font-bold text-gray-700 cursor-pointer">
                                                        Este item é uma doação? <span class="font-normal text-gray-500 block text-[10px] mt-0.5">(Serão gerados automaticamente os Formulários 14.1 e 14.2)</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="bg-white p-5 border-2 border-gray-200 shadow-sm rounded-lg mt-4" :class="tratativa === 'novo' ? 'hidden' : ''">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2 mb-4 text-center">Tratativa a ser Aplicada</p>
                                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-2">
                                                    @foreach([
                                                            'imprimir' => 'Imprimir',
                                                            'encontrado' => 'Encontrado',
                                                            'alterar' => 'Alterar',
                                                            'transferir' => 'Transferir',
                                                            'excluir' => 'Excluir'
                                                        ] as $val => $txt)
                                                                        <label class="flex items-center gap-3 text-xs font-bold text-gray-600 group cursor-pointer bg-gray-50 p-2 rounded border border-transparent hover:border-gray-200 transition-all hover:bg-gray-100">
                                                                            <input type="radio" x-model="tratativa" value="{{ $val }}" class="text-[#004A80] focus:ring-[#004A80] border-gray-300 w-4 h-4 cursor-pointer">
                                                                            <span class="group-hover:text-blue-800" :class="tratativa === '{{ $val }}' ? 'text-blue-800 font-black' : ''">{{ $txt }}</span>
                                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="flex flex-col sm:flex-row gap-3 pt-6 mt-auto">
                                                <button @click="tratativa = 'novo'; selectedIds = []; selectedItem = null;" 
                                                        class="w-full sm:w-auto bg-white border-2 border-amber-500 text-amber-600 hover:text-white px-5 py-4 rounded-lg font-black text-xs sm:text-sm shadow-sm hover:bg-amber-500 transition-all uppercase tracking-widest shrink-0 flex items-center justify-center gap-2 group">
                                                    <span class="text-xl group-hover:scale-125 transition">+</span> NOVO BEM S/ ETIQUETA
                                                </button>
                                                <button @click="saveTratativa()" 
                                                        :disabled="selectedIds.length === 0 && tratativa !== 'novo' || !tratativa || savingTratativa"
                                                        class="flex-1 bg-[#004A80] text-white p-3 rounded-sm font-black text-sm shadow-lg hover:bg-[#003B66] hover:scale-[1.01] transition flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                                    <template x-if="!savingTratativa">
                                                        <div class="flex items-center gap-2">
                                                            <span class="group-hover:rotate-12 transition group-disabled:rotate-0">💾</span>
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
                                            <!-- Cancelar Novo Bem -->
                                            <div class="mt-2 text-center" x-show="tratativa === 'novo'">
                                                 <button @click="tratativa = ''; novaDescricao = ''; novaDependencia = ''; isDoacao = false;"
                                                         class="text-[10px] font-black uppercase tracking-widest text-red-500 hover:text-red-700 hover:underline transition">
                                                     [ X ] CANCELAR OPERAÇÃO DE NOVO BEM
                                                 </button>
                                            </div>
                                        </div>
                                        <!-- End Right: Details & Actions -->

                                    </div>
                                    <!-- End Flex Row (Lists + Form) -->
                                </div>
                                <!-- End Modal Inner Container -->
                            </div>
                            <!-- End Modal Outer Container -->
                        </template>
            </div> <!-- FIM DO MAIN CONTAINER X-DATA -->

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

                                    // Added for Queries
                                    consultaAtiva: 'lista_geral',
                                    consultaPesquisa: '',
                                    dependenciaMapa: null,

                                    init() {
                                        this.dependenciaMapa = {
                                            @foreach(App\Models\Dependencia::all() as $dep)
                                                '{{ $dep->id }}': {!! json_encode($dep->nome) !!},
                                            @endforeach
                                                };
                                },

                                    getDependenciaNome(id) {
                                    if (!id) return 'NÃO DEFINIDO';
                                    if (!this.dependenciaMapa) return id;
                                    return this.dependenciaMapa[id] || id;
                                },

                                getConsultaTitulo() {
                                    const titulos = {
                                        'lista_geral': 'LISTA GERAL DE BENS LIDOS NESTA SESSÃO',
                                        'bens_totalizados': 'LISTA DE BENS TOTALIZADOS GERAIS',
                                        'bens_localizados': 'LISTA DE BENS LOCALIZADOS E CONFERIDOS GERAIS',
                                        'bens_pendentes': 'LISTA DE BENS PENDENTES (NÃO FATURADOS/TRATADOS)',
                                        'lidos_repetidos': 'LISTA DE DIVERGÊNCIAS (ITENS REPETIDOS OU TRANSFERIDOS)',
                                        'dependencias': 'RESUMO DE PROGRESSO POR DEPENDÊNCIA'
                                    };
                                    return titulos[this.consultaAtiva] || 'CONSULTA';
                                },

                                            get filtradosListaGeral() {
                                    return this.history.filter(i => !this.consultaPesquisa || (i.barcode && i.barcode.includes(this.consultaPesquisa)) || (i.descricao && i.descricao.toLowerCase().includes(this.consultaPesquisa.toLowerCase())));
                                },

                                            get filtradosBensTotalizados() {
                                    let map = {};
                                    this.allPendencias.forEach(p => {
                                        let desc = p.bem?.descricao || 'DESCONHECIDO';
                                        let depId = String(p.bem?.id_dependencia || 'SEM DEPENDÊNCIA');
                                        let depNome = this.getDependenciaNome(depId);
                                        let key = desc + '|' + depNome;
                                        if (!map[key]) map[key] = { nome: desc, dependencia: depNome, qtde: 0 };
                                        map[key].qtde++;
                                    });
                                    return Object.values(map)
                                        .filter(i => !this.consultaPesquisa || i.nome.toLowerCase().includes(this.consultaPesquisa.toLowerCase()) || i.dependencia.toLowerCase().includes(this.consultaPesquisa.toLowerCase()))
                                        .sort((a, b) => b.qtde - a.qtde);
                                },

                                            get filtradosBensLocalizados() {
                                    return this.allPendencias
                                        .filter(p => p.status_leitura === 'encontrado' && (!this.consultaPesquisa || (p.bem?.descricao || '').toLowerCase().includes(this.consultaPesquisa.toLowerCase()) || p.id_bem.includes(this.consultaPesquisa)))
                                        .map(p => ({
                                            id_bem: p.id_bem,
                                            descricao: p.bem?.descricao || '--',
                                            dependencia: String(p.bem?.id_dependencia || '')
                                        }));
                                },

                                            get filtradosBensPendentes() {
                                    let pendentes = this.allPendencias.filter(p => (p.status_leitura === 'nao_encontrado' || p.status_leitura === 'novo_sistema') && (!p.tratativa || p.tratativa === 'nenhuma' || p.tratativa === 'cadastrar'));
                                    if (this.consultaPesquisa) {
                                        let search = this.consultaPesquisa.toLowerCase();
                                        pendentes = pendentes.filter(p => p.id_bem.includes(search) || (p.bem?.descricao || '').toLowerCase().includes(search));
                                    }
                                    let grouped = {};
                                    pendentes.forEach(p => {
                                        let depId = String(p.bem?.id_dependencia || 'SEM DEPENDÊNCIA');
                                        if (!grouped[depId]) grouped[depId] = { dependencia: depId, itens: [] };
                                        grouped[depId].itens.push({ id_bem: p.id_bem, descricao: p.bem?.descricao || '--' });
                                    });
                                    return Object.values(grouped).sort((a, b) => String(a.dependencia).localeCompare(String(b.dependencia)));
                                },

                                            get filtradosLidosRepetidos() {
                                    return this.history.filter(i =>
                                        (i.situacao.includes('JÁ CONFERIDO') || i.situacao.includes('DIVERGÊNCIA') || i.is_cross_church) &&
                                        (!this.consultaPesquisa || i.barcode.includes(this.consultaPesquisa) || i.descricao.toLowerCase().includes(this.consultaPesquisa.toLowerCase()))
                                    ).map(i => {
                                        let aviso = '';
                                        if (i.is_cross_church) aviso = "Bem pertence a outra localidade.";
                                        else if (i.situacao.includes('JÁ')) aviso = "Este item já havia sido lido.";
                                        else if (i.situacao.includes('DIVERGÊNCIA')) aviso = "Transferência/Novo Sistema.";

                                        return { ...i, aviso };
                                    });
                                },

                                            get filtradosDependencias() {
                                    let map = {};
                                    this.allPendencias.forEach(p => {
                                        let depId = String(p.bem?.id_dependencia || 'N/A');
                                        if (!map[depId]) map[depId] = { codigo: depId, nome: this.getDependenciaNome(depId), localizados: 0, pendentes: 0 };

                                        if (p.status_leitura === 'encontrado' || (p.tratativa && p.tratativa !== 'nenhuma' && p.tratativa !== 'cadastrar')) {
                                            map[depId].localizados++;
                                        } else {
                                            map[depId].pendentes++;
                                        }
                                    });
                                    return Object.values(map)
                                        .filter(d => !this.consultaPesquisa || d.codigo.includes(this.consultaPesquisa) || d.nome.toLowerCase().includes(this.consultaPesquisa.toLowerCase()))
                                        .sort((a, b) => String(a.codigo).localeCompare(String(b.codigo)));
                                },

                                filtrarPorDependencia(codigo) {
                                    if (codigo === 'N/A') return;
                                    this.consultaPesquisa = codigo;
                                    this.consultaAtiva = 'bens_pendentes';
                                },

                                openPendencia(barcode) {
                                    this.searchPendencia = barcode;
                                    this.showPendencias = true;
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
                                            const locationTip = data.is_global ? "\n\n⚠️ ITEM FORA DESTE INVENTÁRIO (Cria divergência)" : "";
                                            confirmAction(
                                                'Confirmar Registro',
                                                `Deseja registrar a conferência do bem:\n\n[ ${data.id_bem} ]\n${data.descricao}${locationTip}`,
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
                                                                        <span class="text-gray-400">➕</span>
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
                                                    title: 'Selecione o Bem Móvel',
                                                    html: 'Foram encontrados múltiplos itens globais:' + optionsHtml,
                                                    icon: 'info',
                                                    showConfirmButton: false,
                                                    showCloseButton: true
                                                });
                                            } else {
                                                this.searchPendencia = this.searchText;
                                                this.showPendencias = true;
                                                Toast.fire({
                                                    icon: 'info',
                                                    title: 'Vários itens encontrados no inventário. Verifique na lista.'
                                                });
                                            }
                                        }
                                    } catch (err) {
                                        Toast.fire({ icon: 'error', title: 'Erro na busca por texto' });
                                    }
                                },

                                isValidToSave() {
                                    if (this.selectedIds.length === 0 && this.tratativa !== 'novo') return false;
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
                                    return count > 1 ? `APLICAR AOS ${count} ITENS` : `SALVAR MUDANÇAS`;
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

                                            if (this.tratativa === 'novo' && count === 0) {
                                                this.stats.novos++;
                                                this.stats.bensFinal++;
                                                this.stats.resultado = Math.round((this.stats.bensFinal / this.stats.bensInicial) * 100);
                                                
                                                this.history.unshift({
                                                    barcode: data.new_detalhe ? data.new_detalhe.id_bem : 'NOVO BEM',
                                                    descricao: this.novaDescricao || 'NOVO BEM SEM ETIQUETA',
                                                    dependencia: this.novaDependencia,
                                                    situacao: 'CONFERIDO - NOVO',
                                                    is_cross_church: false,
                                                    lido: true
                                                });

                                                if (data.new_detalhe) {
                                                    this.allPendencias.unshift(data.new_detalhe);
                                                }
                                            }

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
                                                    title: 'Doação Registrada!',
                                                    html: `
                                                                    <p class="mb-3">${data.message}</p>
                                                                    <div class="bg-blue-50 p-4 rounded border border-blue-200">
                                                                        <p class="font-bold text-sm mb-2">📄 Formulários de Doação Gerados:</p>
                                                                        <a href="${data.donation_pdfs.form_14_1}" target="_blank" class="block bg-blue-600 text-white px-4 py-2 rounded mb-2 hover:bg-blue-700">
                                                                            📥 Baixar Formulário 14.1 (Declaração de Doação)
                                                                        </a>
                                                                        <a href="${data.donation_pdfs.form_14_2}" target="_blank" class="block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                                            📥 Baixar Formulário 14.2 (Ocorrência de Entrada)
                                                                        </a>
                                                                    </div>
                                                                `,
                                                    confirmButtonText: 'OK'
                                                });
                                            }

                                            // Handle Generated Forms (14.3, 14.6, 14.7)
                                            if (data.generated_forms && data.generated_forms.length > 0) {
                                                let formHtml = '<div class="bg-gray-50 p-4 rounded border border-gray-200 text-left mt-4">';
                                                formHtml += '<p class="font-bold text-sm mb-2 text-gray-700">📄 Documentos Sugeridos:</p>';
                                                data.generated_forms.forEach(f => {
                                                    formHtml += `<a href="${f.url}" target="_blank" class="block bg-gray-700 text-white px-4 py-2 rounded mb-2 hover:bg-black text-[10px] font-bold uppercase transition">📥 ${f.label}</a>`;
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
                                        if (overlayInput) overlayInput.focus();
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
                                            console.error("Erro ao iniciar câmera:", err);
                                            this.cameraActive = false;
                                            showAlert('Erro na Câmera', 'error', 'Não foi possível acessar a câmera do dispositivo.');
                                        });
                                    });
                                },

                                stopCamera() {
                                    if (this.html5QrCode) {
                                        this.html5QrCode.stop().then(() => {
                                            this.cameraActive = false;
                                            this.html5QrCode = null;
                                            this.focusScanner();
                                        }).catch(err => console.error("Erro ao parar câmera", err));
                                    } else {
                                        this.cameraActive = false;
                                    }
                                },

                                confirmFinalize() {
                                    confirmAction(
                                        'Finalizar Inventário',
                                        'ATENÇÃO: Deseja realmente finalizar este inventário? Após esta ação, NENHUMA alteração física ou leitura poderá ser realizada. Confirma?',
                                        () => {
                                            document.getElementById('finalizeForm').submit();
                                        }
                                    );
                                },

                                async processScan() {
                                    if (this.loading || !this.barcode.trim()) return;
                                    if (this.exigirDependencia && !this.dependenciaId) {
                                        showAlert('Atenção', 'warning', 'Selecione a dependência física onde você se encontra.');
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
                                                descricao: data.bem ? (data.bem.descricao || 'ITEM SEM CADASTRO') : 'ITEM NÃO ENCONTRADO',
                                                situacao: data.status === 'success' ? 'CONFERIDO' : (data.status === 'warning' ? 'JÁ CONFERIDO' : 'DIVERGÊNCIA')
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
                                                this.showToastOverlay('Erro', data.message, '❌');
                                            } else {
                                                playSuccess();
                                                this.showToastOverlay(data.status === 'success' ? 'Sucesso' : 'Aviso', data.message, data.status === 'success' ? '✅' : '⚠️');
                                            }

                                            this.barcode = '';
                                        }
                                    } catch (error) {
                                        console.error('Erro no process', error);
                                        Toast.fire({ icon: 'error', title: 'Erro de comunicação.' });
                                        this.showToastOverlay('Erro', 'Falha ao comunicar porta.', '❌');
                                    } finally {
                                        this.loading = false;
                                        this.focusScanner();
                                    }
                                },

                                showToastOverlay(title, msg, icon) {
                                    if (!this.showScannerManual) return;
                                    this.feedbackTitle = title;
                                    this.feedbackMessage = msg;
                                    this.feedbackIcon = icon;
                                    this.showFeedback = true;
                                    setTimeout(() => {
                                        this.showFeedback = false;
                                    }, 2500);
                                }
                            }
                                                    }
                        </script>
@endsection