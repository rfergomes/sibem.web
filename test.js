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
        
