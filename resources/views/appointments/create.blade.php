@extends('layouts.app')

@section('title', 'Novo Agendamento')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6 animate-fadeIn" x-data="{
                    locais: {{ Js::from($locais) }},
                    allSetores: {{ Js::from($setores) }},
                    allIgrejas: {{ Js::from($igrejas) }},
                    selectedLocal: '',
                    selectedSetor: '',
                    search: '',
                    open: false,
                    selectedIgrejaId: '', 
                    selectedIgrejaName: 'Selecione a localidade...',

                    get filteredSetores() {
                        if (!this.selectedLocal) return this.allSetores;
                        const localIgrejas = this.allIgrejas.filter(i => i.local_id == this.selectedLocal);
                        return [...new Set(localIgrejas.map(i => i.setor).filter(s => s))].sort();
                    },

                    get filteredIgrejas() {
                        return this.allIgrejas.filter(i => {
                            const matchesLocal = this.selectedLocal === '' || i.local_id == this.selectedLocal;
                            const matchesSetor = this.selectedSetor === '' || i.setor == this.selectedSetor;
                            const matchesSearch = this.search === '' || 
                                                  i.nome.toLowerCase().includes(this.search.toLowerCase()) || 
                                                  (i.cod_siga && String(i.cod_siga).toLowerCase().includes(this.search.toLowerCase()));
                            return matchesLocal && matchesSetor && matchesSearch;
                        });
                    },

                    selectIgreja(igreja) {
                        this.selectedIgrejaId = igreja.id;
                        this.selectedIgrejaName = igreja.full_name;
                        this.open = false;
                        this.search = '';
                    },

                    init() {
                        // Initialize if re-loading (e.g. validacao faill) - Optional expansion
                        // Watchers for resetting filters if upper filter changes?
                        this.$watch('selectedLocal', () => this.selectedSetor = '');
                    }
                }">
        <div class="flex items-center gap-4">
            <a href="{{ route('appointments.index') }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-xl transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Programar Inventário</h2>
                <p class="text-sm text-gray-500">Inicie um agendamento como previsão para posterior confirmação.</p>
            </div>
        </div>

        <form action="{{ route('appointments.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-visible relative z-10">
            @csrf
            <div class="p-8 space-y-6">
                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2 tracking-wide">Filtrar por
                            Administração</label>
                        <select x-model="selectedLocal"
                            class="w-full bg-white border border-gray-200 rounded-lg text-sm px-3 py-2 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                            <option value="">Todas as Administrações</option>
                            <template x-for="local in locais" :key="local.id">
                                <option :value="local.id" x-text="local.nome"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2 tracking-wide">Filtrar por
                            Setor</label>
                        <select x-model="selectedSetor"
                            class="w-full bg-white border border-gray-200 rounded-lg text-sm px-3 py-2 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                            <option value="">Todos os Setores</option>
                            <template x-for="setor in filteredSetores" :key="setor">
                                <option :value="setor" x-text="setor"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Custom Church Select -->
                    <div class="col-span-1 md:col-span-2 relative">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Localidade /
                            Igreja</label>
                        <input type="hidden" name="igreja_id" :value="selectedIgrejaId" required>

                        <div class="relative" @click.away="open = false">
                            <button type="button" @click="open = !open"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 flex items-center justify-between text-left">
                                <span x-text="selectedIgrejaName"
                                    :class="{'text-gray-400': !selectedIgrejaId, 'text-gray-900': selectedIgrejaId}"></span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{'rotate-180': open}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open"
                                class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-2xl border border-gray-100 max-h-80 flex flex-col animate-scaleIn origin-top">

                                <!-- Search Box -->
                                <div class="p-3 border-b border-gray-100 sticky top-0 bg-white rounded-t-xl z-10">
                                    <div class="relative">
                                        <input type="text" x-model="search"
                                            class="w-full pl-10 pr-8 py-2.5 text-sm bg-gray-50 border-none rounded-lg focus:ring-2 focus:ring-blue-100 text-gray-700 font-medium placeholder-gray-400"
                                            placeholder="Pesquise por nome, código ou cidade..." autofocus>
                                        <div
                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <button type="button" @click="search = ''" x-show="search.length > 0"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- List Options -->
                                <div class="overflow-y-auto flex-1 p-1 custom-scrollbar">
                                    <template x-for="igreja in filteredIgrejas" :key="igreja.id">
                                        <div @click="selectIgreja(igreja)"
                                            class="cursor-pointer px-3 py-2.5 rounded-lg hover:bg-blue-50 transition-colors group flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-blue-100 flex items-center justify-center text-gray-400 group-hover:text-blue-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-700 group-hover:text-blue-700"
                                                        x-text="igreja.nome"></span>
                                                    <span
                                                        class="text-[10px] uppercase font-bold text-gray-400 group-hover:text-blue-400"
                                                        x-text="igreja.local_nome + (igreja.setor ? ' • ' + igreja.setor : '')"></span>
                                                </div>
                                            </div>
                                            <div x-show="selectedIgrejaId == igreja.id" class="text-blue-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="filteredIgrejas.length === 0" class="px-6 py-8 text-center text-gray-400">
                                        <p class="text-sm">Nenhuma igreja encontrada.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Responsável -->
                    <div>
                        <label for="responsavel_nome"
                            class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Nome do
                            Responsável</label>
                        <input type="text" name="responsavel_nome" id="responsavel_nome" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                            placeholder="Ex: Ir. João Carlos">
                    </div>

                    <!-- Cargo -->
                    <div>
                        <label for="responsavel_cargo"
                            class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Cargo /
                            Função</label>
                        <input type="text" name="responsavel_cargo" id="responsavel_cargo"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                            placeholder="Ex: Ancião, Diácono, Responsável Local...">
                    </div>

                    <!-- Contato -->
                    <div>
                        <label for="responsavel_contato"
                            class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Telefone /
                            WhatsApp</label>
                        <input type="text" name="responsavel_contato" id="responsavel_contato"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                            placeholder="Ex: (19) 99999-8888">
                    </div>

                    <!-- Data e Hora -->
                    <div>
                        <label for="scheduled_at"
                            class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Data e
                            Horário</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                    </div>
                </div>

                <!-- Notas -->
                <div>
                    <label for="notes"
                        class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Observações
                        Extras</label>
                    <textarea name="notes" id="notes" rows="4"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                        placeholder="Alguma informação importante para a equipe que fará a viagem?"></textarea>
                </div>
            </div>

            <div class="px-8 py-5 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('appointments.index') }}"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-gray-500 hover:bg-gray-600 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95">Cancelar</a>
                <button type="submit"
                    class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95">
                    Salvar Agendamento
                </button>
            </div>
        </form>
    </div>
@endsection