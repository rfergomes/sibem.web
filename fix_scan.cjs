const fs = require('fs');

const currentBladePath = 'resources/views/inventarios/scan.blade.php';
const oldBladePath = 'scan_old.blade.php';

const currentBlade = fs.readFileSync(currentBladePath, 'utf8');
const oldBlade = fs.readFileSync(oldBladePath, 'utf8');

// Extract Modal from old blade
const modalStart = oldBlade.indexOf('<template x-teleport="body">');
const modalEnd = oldBlade.indexOf('</template>', modalStart) + '</template>'.length;
const modalTemplate = oldBlade.substring(modalStart, modalEnd);

// Find NEW MAIN AREA in current blade
const newMainStart = currentBlade.indexOf('<!-- NEW MAIN AREA: BENS / TRATATIVAS (Replaces Pendencias SPA Tab) -->');
if (newMainStart === -1) {
    console.error("Could not find NEW MAIN AREA marker");
    process.exit(1);
}

const scriptTagIndex = currentBlade.lastIndexOf('<script>');
const newMainEnd = currentBlade.lastIndexOf('</div>', scriptTagIndex);
if (newMainEnd === -1) {
    console.error("Could not find outer div end marker");
    process.exit(1);
}

// We define our new Home area (Consultas Area) to replace the NEW MAIN AREA:
const newHomeArea = `
        <!-- CONSULTAS DASHBOARD OVERVIEW -->
        <div class="flex-grow flex flex-col overflow-hidden bg-[#F5F7FA] rounded-xl relative shadow-md animate-fade-in border border-blue-900/20" x-show="!showScannerManual && !cameraActive">
            <!-- Header Toolbar -->
            <div class="bg-gray-100 border-b border-gray-200 p-3 flex justify-between items-center z-10 shrink-0">
                <div class="flex items-center gap-2 md:gap-4">
                    <div class="flex flex-col">
                        <label class="text-[9px] font-black uppercase text-gray-500 mb-0.5 ml-1">Pesquisar</label>
                        <div class="relative">
                            <input type="text" x-model="consultaPesquisa" placeholder="Digite aqui..." class="text-[11px] border-gray-300 rounded shadow-inner py-1.5 w-32 md:w-48 uppercase font-bold focus:ring-[#004A80] focus:border-[#004A80]">
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-[9px] font-black uppercase text-gray-500 mb-0.5 ml-1">Selecionar Consulta</label>
                        <select x-model="consultaAtiva" class="text-[11px] border-gray-300 rounded shadow-inner py-1.5 w-40 md:w-56 uppercase font-black focus:ring-[#004A80] focus:border-[#004A80] text-[#004A80]">
                            <option value="lista_geral">BENS LIDOS (HISTÓRICO)</option>
                            <option value="bens_totalizados">BENS TOTALIZADOS</option>
                            <option value="bens_localizados">BENS LOCALIZADOS</option>
                            <option value="bens_pendentes">BENS PENDENTES / NÃO LIDOS</option>
                            <option value="lidos_repetidos">LIDOS REPETIDOS / DIVERGÊNCIA</option>
                            <option value="dependencias">DEPENDÊNCIAS DA TAREFA</option>
                        </select>
                    </div>
                    <button class="bg-gray-200 text-[#004A80] p-1.5 mt-4 rounded shadow-sm hover:bg-gray-300 transition" @click="consultaPesquisa = ''" title="Limpar Pesquisa">
                        <span class="text-lg font-black leading-none">↺</span>
                    </button>
                    <!-- Global/Server Search -->
                    <div class="flex flex-col relative ml-2 group hidden md:flex">
                         <div class="absolute bottom-full mb-1 left-0 bg-gray-800 text-white text-[9px] p-2 rounded hidden group-hover:block w-48 shadow-lg">Busca no servidor de itens fora da dependência atual.</div>
                         <button @click="abrirScannerManual()" class="bg-[#004A80] text-white py-1.5 px-3 rounded shadow-sm mt-4 hover:bg-[#003B66] text-[10px] font-black uppercase tracking-widest flex items-center gap-1 transition">
                             <span class="text-sm border-r border-blue-400 pr-2 mr-1">🔍</span> Consultar Banco
                         </button>
                    </div>
                </div>

                <div class="flex gap-2 items-end">
                    <button @click="showPendencias = true" class="bg-white border-b-4 border-red-700 hover:bg-red-50 text-red-700 font-black px-4 py-2 rounded-lg text-[10px] sm:text-xs uppercase shadow-sm flex items-center gap-2 transition group">
                        <span class="text-lg sm:text-xl group-hover:scale-110 transition">❗</span> PENDÊNCIAS
                    </button>
                </div>
            </div>

            <!-- Content Area (Tables) -->
            <div class="flex-grow overflow-hidden flex flex-col bg-white">
                <div class="bg-gradient-to-r from-[#004A80] to-[#003B66] text-white text-center text-[10px] sm:text-xs font-black py-1.5 border-b border-blue-900 uppercase tracking-[0.2em] shadow-sm shrink-0" x-text="getConsultaTitulo()"></div>
                
                <div class="overflow-y-auto flex-grow custom-scrollbar relative">
                    <!-- 1. LISTA GERAL -->
                    <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'lista_geral'">
                        <thead class="bg-blue-50/90 text-gray-500 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm">
                            <tr>
                                <th class="p-2 border-r border-[#004A80]/10 w-32 font-black pl-4">Etiqueta</th>
                                <th class="p-2 border-r border-[#004A80]/10 font-black pl-4">Bem Móvel</th>
                                <th class="p-2 border-r border-[#004A80]/10 w-48 hidden md:table-cell font-black pl-4">Dependência</th>
                                <th class="p-2 border-r border-[#004A80]/10 w-32 font-black pl-4 hidden sm:table-cell">Situação</th>
                                <th class="p-2 w-16 text-center font-black">Lido</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in filtradosListaGeral" :key="idx + '-' + item.barcode">
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-2 border-r border-gray-100 font-mono font-bold pl-4" :class="item.is_cross_church ? 'text-red-700 font-black' : 'text-[#004A80]'" x-text="item.barcode"></td>
                                    <td class="p-2 border-r border-gray-100 uppercase" x-text="item.descricao"></td>
                                    <td class="p-2 border-r border-gray-100 uppercase font-bold text-gray-600 hidden md:table-cell" x-text="getDependenciaNome(item.dependencia)"></td>
                                    <td class="p-2 border-r border-gray-100 hidden sm:table-cell">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest" :class="item.situacao.includes('DIVERGÊNCIA') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'" x-text="item.situacao"></span>
                                    </td>
                                    <td class="p-2 text-center text-lg"><span x-show="item.lido">✔️</span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <!-- 2. BENS TOTALIZADOS -->
                    <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'bens_totalizados'" x-cloak>
                        <thead class="bg-blue-50/90 text-gray-500 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm">
                            <tr>
                                <th class="p-2 border-r border-[#004A80]/10 font-black pl-4">Bem Móvel</th>
                                <th class="p-2 border-r border-[#004A80]/10 w-64 font-black pl-4">Dependência</th>
                                <th class="p-2 w-24 text-center font-black">Qtde</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, idx) in filtradosBensTotalizados" :key="idx + '-' + item.nome">
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-2 border-r border-gray-100 uppercase font-bold pl-4" x-text="item.nome"></td>
                                    <td class="p-2 border-r border-gray-100 uppercase font-black text-gray-600 pl-4" x-text="item.dependencia"></td>
                                    <td class="p-2 text-center font-black text-[#004A80] bg-blue-50/30 table-cell" x-text="item.qtde"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <!-- 3. BENS LOCALIZADOS -->
                    <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'bens_localizados'" x-cloak>
                        <thead class="bg-green-50/90 text-green-700 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm border-b border-green-200">
                            <tr>
                                <th class="p-2 border-r border-green-200 w-32 font-black pl-4">Etiqueta</th>
                                <th class="p-2 border-r border-green-200 font-black pl-4">Bem Móvel</th>
                                <th class="p-2 border-r border-green-200 w-48 font-black pl-4 hidden sm:table-cell">Dependência Original</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="item in filtradosBensLocalizados" :key="item.id_bem">
                                <tr class="hover:bg-green-50 transition border-l-2 border-transparent hover:border-green-500">
                                    <td class="p-2 border-r border-gray-100 font-mono font-black text-[#004A80] pl-4" x-text="item.id_bem"></td>
                                    <td class="p-2 border-r border-gray-100 uppercase" x-text="item.descricao"></td>
                                    <td class="p-2 border-r border-gray-100 uppercase font-bold text-gray-600 hidden sm:table-cell pl-4" x-text="getDependenciaNome(item.dependencia)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <!-- 4. BENS PENDENTES (Grouped by Dependencia) -->
                    <div x-show="consultaAtiva === 'bens_pendentes'" x-cloak>
                        <template x-for="(grupo, idx) in filtradosBensPendentes" :key="idx + '-' + grupo.dependencia">
                            <div class="mb-4">
                                <div class="bg-[#F0F4F8] px-4 py-2 flex justify-between border-y border-[#004A80]/20 sticky top-0 z-10 shadow-sm backdrop-blur-sm">
                                    <span class="text-[10px] md:text-[11px] font-black text-[#004A80] tracking-widest uppercase">--- <span x-text="getDependenciaNome(grupo.dependencia)"></span> ---</span>
                                    <span class="text-[10px] font-black text-red-600 uppercase" x-text="grupo.itens.length + ' PENDENTES'"></span>
                                </div>
                                <table class="w-full text-left border-collapse text-[11px]">
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="item in grupo.itens" :key="item.id_bem">
                                            <tr class="hover:bg-red-50 transition pl-4 border-l-2 border-transparent hover:border-red-500 cursor-pointer" @click="openPendencia(item.id_bem)">
                                                <td class="p-2 border-r border-gray-100 font-mono text-red-800 w-32 pl-6 font-bold hover:underline" x-text="item.id_bem" title="Clique para abrir Pendências"></td>
                                                <td class="p-2 border-r border-gray-100 uppercase text-gray-700 pl-4" x-text="item.descricao"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </div>

                    <!-- 4.B LIDOS REPETIDOS / DIVERGENCIA -->
                    <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'lidos_repetidos'" x-cloak>
                        <thead class="bg-amber-50/90 text-amber-800 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm border-b border-amber-200">
                            <tr>
                                <th class="p-2 border-r border-amber-200/50 w-32 font-black pl-4">Etiqueta</th>
                                <th class="p-2 border-r border-amber-200/50 font-black pl-4">Bem Móvel / Aviso</th>
                                <th class="p-2 border-r border-amber-200/50 w-48 font-black pl-4 hidden sm:table-cell">Dependência Lida</th>
                                <th class="p-2 border-r border-amber-200/50 w-32 font-black pl-4 hidden md:table-cell">Situação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-100/50">
                            <template x-for="item in filtradosLidosRepetidos" :key="item.barcode">
                                <tr class="hover:bg-amber-50/80 transition bg-amber-50/30 border-l-2 border-amber-400">
                                    <td class="p-2 border-r border-amber-100/50 font-mono font-black text-amber-900 pl-4" x-text="item.barcode"></td>
                                    <td class="p-2 border-r border-amber-100/50 uppercase pl-4">
                                        <span x-text="item.descricao" class="font-bold text-gray-800"></span>
                                        <div x-show="item.aviso" class="text-[9px] text-red-600 mt-1 uppercase font-black tracking-tighter" x-text="item.aviso"></div>
                                    </td>
                                    <td class="p-2 border-r border-amber-100/50 uppercase font-bold text-amber-700 hidden sm:table-cell pl-4" x-text="getDependenciaNome(item.dependencia)"></td>
                                    <td class="p-2 border-r border-amber-100/50 hidden md:table-cell pl-4">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-700" x-text="item.situacao"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <!-- 5. DEPENDÊNCIAS -->
                    <table class="w-full text-left border-collapse text-[11px]" x-show="consultaAtiva === 'dependencias'" x-cloak>
                        <thead class="bg-blue-50/90 text-gray-500 sticky top-0 uppercase tracking-widest text-[9px] shadow-sm z-10 backdrop-blur-sm">
                            <tr>
                                <th class="p-2 border-r border-[#004A80]/10 w-24 font-black text-center">Código</th>
                                <th class="p-2 border-r border-[#004A80]/10 font-black pl-4">Dependência</th>
                                <th class="p-2 border-r border-[#004A80]/10 w-24 text-center font-black">Conf. / Tratado</th>
                                <th class="p-2 w-24 text-center font-black">Pendentes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="dep in filtradosDependencias" :key="dep.codigo">
                                <tr class="hover:bg-blue-50 transition border-l-4 border-transparent hover:border-[#004A80] cursor-pointer" @click="filtrarPorDependencia(dep.codigo)" title="Clique para filtrar pendentes nesta dependência">
                                    <td class="p-2 border-r border-gray-100 font-mono text-gray-500 font-bold text-center bg-gray-50/50" x-text="dep.codigo"></td>
                                    <td class="p-2 border-r border-gray-100 uppercase font-bold pl-4 text-blue-900" x-text="dep.nome"></td>
                                    <td class="p-2 text-center text-green-700 font-black border-r border-gray-100 bg-green-50/30" x-text="dep.localizados"></td>
                                    <td class="p-2 text-center text-red-600 font-black bg-red-50/30" x-text="dep.pendentes"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary (Optional) -->
                <div class="bg-gray-100 p-1.5 border-t border-gray-300 text-right text-[10px] font-black text-gray-500 uppercase tracking-widest shadow-inner relative z-10 flex justify-between px-4">
                    <span>SISTEMA DE BENS MÓVEIS</span>
                    <span x-show="consultaAtiva === 'lista_geral'" x-text="filtradosListaGeral.length + ' Itens'"></span>
                    <span x-show="consultaAtiva === 'bens_totalizados'" x-text="filtradosBensTotalizados.length + ' Grupos'"></span>
                    <span x-show="consultaAtiva === 'bens_localizados'" x-text="filtradosBensLocalizados.length + ' Itens'"></span>
                    <!-- bens pendentes length would be group count, omit or keep empty -->
                    <span x-show="consultaAtiva === 'lidos_repetidos'" x-text="filtradosLidosRepetidos.length + ' Itens'"></span>
                    <span x-show="consultaAtiva === 'dependencias'" x-text="filtradosDependencias.length + ' Locais'"></span>
                </div>
            </div>
            
${modalTemplate}
        </div>
`;

// Insert the new variables and functions into inventarioScanner()
const scriptInsertionStart = currentBlade.indexOf('history: @json($historyInitial),');
// Note: We're replacing from '\n                history: @json($historyInitial),'
const extraMapBladeStr = `
                // Added for Queries
                consultaAtiva: 'lista_geral',
                consultaPesquisa: '',
                dependenciaMapa: null,

                init() {
                    this.dependenciaMapa = {
                        @foreach(App\\Models\\Dependencia::all() as $dep)
                            '{{ $dep->id }}': '{{ addslashes($dep->nome) }}',
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
                        .sort((a,b) => b.qtde - a.qtde);
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
                        pendentes = pendentes.filter(p => p.id_bem.includes(search) || (p.bem?.descricao||'').toLowerCase().includes(search));
                    }
                    let grouped = {};
                    pendentes.forEach(p => {
                        let depId = String(p.bem?.id_dependencia || 'SEM DEPENDÊNCIA');
                        if (!grouped[depId]) grouped[depId] = { dependencia: depId, itens: [] };
                        grouped[depId].itens.push({ id_bem: p.id_bem, descricao: p.bem?.descricao || '--' });
                    });
                    return Object.values(grouped).sort((a,b) => String(a.dependencia).localeCompare(String(b.dependencia)));
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
                        .sort((a,b) => String(a.codigo).localeCompare(String(b.codigo)));
                },

                filtrarPorDependencia(codigo) {
                    if(codigo === 'N/A') return;
                    this.consultaPesquisa = codigo;
                    this.consultaAtiva = 'bens_pendentes';
                },

                openPendencia(barcode) {
                    this.searchPendencia = barcode;
                    this.showPendencias = true;
                },
`;

const resultBlade = currentBlade.substring(0, newMainStart) +
    newHomeArea +
    currentBlade.substring(newMainEnd, scriptInsertionStart) +
    extraMapBladeStr +
    currentBlade.substring(scriptInsertionStart);

fs.writeFileSync(currentBladePath, resultBlade);
console.log('Update successful, wrote the new layout and queries logic.');
