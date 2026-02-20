@extends('layouts.app')

@section('title', 'Detalhes do Agendamento')

@section('content')
    <div class="max-w-4xl mx-auto space-y-8 animate-fadeIn"
        x-data="{
            showWhatsAppModal: false,
            activeTab: 'confirmacao',
            copied: false,
            messages: {
                confirmacao: `{{ addslashes($whatsappMessage) }}`,
                convite: `{{ addslashes($inviteMessage) }}`
            },
            get currentMessage() { return this.messages[this.activeTab]; },
            copyMessage() {
                navigator.clipboard.writeText(this.currentMessage).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                    Toast.fire({ icon: 'success', title: 'Mensagem copiada!' });
                });
            },
            openWhatsApp() {
                const phone = '{{ preg_replace('/[^0-9]/', '', $appointment->responsavel_contato ?? '') }}';
                const url = 'https://wa.me/55' + phone + '?text=' + encodeURIComponent(this.currentMessage);
                window.open(url, '_blank');
            }
        }">

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('appointments.index') }}"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $appointment->igreja ? $appointment->igreja->nome : ($appointment->local->nome ?? 'Local Não Identificado') }}
                    </h2>
                    @if($appointment->igreja)
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">{{ $appointment->local->nome }}</p>
                    @endif
                    <p class="text-sm text-gray-500">Gestão de contato e integração WhatsApp.</p>
                </div>
            </div>

            @php
                $statusClasses = [
                    'previsao' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'confirmado' => 'bg-green-50 text-green-600 border-green-100',
                    'cancelado' => 'bg-red-50 text-red-600 border-red-100',
                    'adiado' => 'bg-orange-50 text-orange-600 border-orange-100',
                ];
                $statusLabels = [
                    'previsao' => 'Previsão',
                    'confirmado' => 'Confirmado',
                    'cancelado' => 'Cancelado',
                    'adiado' => 'Adiado',
                ];
            @endphp
            <span
                class="px-6 py-2 rounded-full border text-sm font-bold uppercase tracking-widest {{ $statusClasses[$appointment->status] ?? 'bg-gray-50 text-gray-500 border-gray-200' }}">
                {{ $statusLabels[$appointment->status] ?? 'INDEFINIDO' }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Detalhes do Contato -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-8">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Informações de Contato
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 font-bold text-xl">
                                    {{ substr($appointment->responsavel_nome, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-lg leading-tight">
                                        {{ $appointment->responsavel_nome }}
                                    </p>
                                    <p class="text-sm text-gray-500 font-medium uppercase tracking-tight">
                                        {{ $appointment->responsavel_cargo ?? 'Responsável Local' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-gray-600">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="font-medium">{{ $appointment->responsavel_contato ?? 'Não Informado' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Data Planejada</h3>
                        <div class="flex items-center gap-3 text-2xl font-bold text-gray-700">
                            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $appointment->scheduled_at ? $appointment->scheduled_at->format('d/m/Y H:i') : 'Data a definir' }}
                        </div>
                    </div>

                    @if($appointment->justification)
                        <div class="pt-6 border-t border-gray-50">
                            <h3 class="text-xs font-bold text-red-400 uppercase tracking-[0.2em] mb-2">Justificativa da
                                Alteração</h3>
                            <p class="text-sm text-gray-600 italic leading-relaxed">
                                "{{ $appointment->justification }}"
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- WhatsApp Launch Card -->
            <div class="bg-emerald-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-48 h-48 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col h-full gap-6">
                    <div class="flex items-center gap-3">
                        <div class="bg-emerald-500 p-2 rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 2c-5.506 0-9.969 4.463-9.969 9.969 0 1.763.457 3.42 1.257 4.866l-1.334 4.866 4.968-1.303c1.403.766 3.003 1.203 4.706 1.203 5.506 0 10.151-4.463 10.151-10.151 0-5.506-4.646-9.969-10.151-9.969zm5.95 13.911c-.244.686-1.42 1.282-1.956 1.344-.537.062-1.031.282-3.414-.703-2.868-1.187-4.681-4.094-4.825-4.288-.144-.194-1.169-1.556-1.169-2.969 0-1.413.744-2.106 1.013-2.4.269-.294.625-.369.831-.369.213 0 .425.006.606.013.194.006.45-.075.706.544.263.631.894 2.181.975 2.338.081.156.138.344.031.544-.406.819-.594.981-1.131 1.631-.131.156-.269.325-.112.594.156.269.694 1.144 1.488 1.85 1.025.913 1.888 1.194 2.156 1.331.269.138.425.113.581-.069.156-.181.669-.781.85-.1.181.681-.594.344-1.144.5 1.144.156 1.819-.006 2.131-.244.313-.244.669-.744.913-1.431z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold">Mensagens WhatsApp</h2>
                    </div>

                    <p class="text-emerald-200 text-sm leading-relaxed">
                        Selecione o tipo de mensagem para enviar ao responsável ou compartilhar com a comunidade.
                    </p>

                    <div class="space-y-3 flex-1">
                        <!-- Confirmation Message -->
                        <button @click="activeTab = 'confirmacao'; showWhatsAppModal = true"
                            class="w-full flex items-center gap-4 bg-white/10 hover:bg-white/20 rounded-2xl p-4 text-left transition-all group">
                            <div class="w-10 h-10 bg-blue-400/30 rounded-xl flex items-center justify-center shrink-0">
                                📋
                            </div>
                            <div>
                                <p class="font-bold text-white">Mensagem de Confirmação</p>
                                <p class="text-xs text-emerald-300">Para o responsável — confirmar data e presença</p>
                            </div>
                            <svg class="w-5 h-5 text-white/40 group-hover:text-white ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Invite Message -->
                        <button @click="activeTab = 'convite'; showWhatsAppModal = true"
                            class="w-full flex items-center gap-4 bg-white/10 hover:bg-white/20 rounded-2xl p-4 text-left transition-all group">
                            <div class="w-10 h-10 bg-yellow-400/30 rounded-xl flex items-center justify-center shrink-0">
                                🙏
                            </div>
                            <div>
                                <p class="font-bold text-white">Mensagem de Convite</p>
                                <p class="text-xs text-emerald-300">Para o grupo — convidar irmãos para o inventário</p>
                            </div>
                            <svg class="w-5 h-5 text-white/40 group-hover:text-white ml-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp Modal -->
        <div x-show="showWhatsAppModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @keydown.escape.window="showWhatsAppModal = false">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showWhatsAppModal = false"></div>

            <!-- Modal Content -->
            <div class="relative bg-emerald-900 rounded-3xl shadow-2xl w-full max-w-lg text-white overflow-hidden"
                x-show="showWhatsAppModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <!-- Modal Header with Tabs -->
                <div class="p-6 border-b border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="bg-emerald-500 p-1.5 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.031 2c-5.506 0-9.969 4.463-9.969 9.969 0 1.763.457 3.42 1.257 4.866l-1.334 4.866 4.968-1.303c1.403.766 3.003 1.203 4.706 1.203 5.506 0 10.151-4.463 10.151-10.151 0-5.506-4.646-9.969-10.151-9.969zm5.95 13.911c-.244.686-1.42 1.282-1.956 1.344-.537.062-1.031.282-3.414-.703-2.868-1.187-4.681-4.094-4.825-4.288-.144-.194-1.169-1.556-1.169-2.969 0-1.413.744-2.106 1.013-2.4.269-.294.625-.369.831-.369.213 0 .425.006.606.013.194.006.45-.075.706.544.263.631.894 2.181.975 2.338.081.156.138.344.031.544-.406.819-.594.981-1.131 1.631-.131.156-.269.325-.112.594.156.269.694 1.144 1.488 1.85 1.025.913 1.888 1.194 2.156 1.331.269.138.425.113.581-.069.156-.181.669-.781.85-.1.181.681-.594.344-1.144.5 1.144.156 1.819-.006 2.131-.244.313-.244.669-.744.913-1.431z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-lg">Mensagem WhatsApp</h3>
                        </div>
                        <button @click="showWhatsAppModal = false"
                            class="p-2 text-white/50 hover:text-white hover:bg-white/10 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="flex gap-2">
                        <button @click="activeTab = 'confirmacao'"
                            :class="activeTab === 'confirmacao' ? 'bg-white text-emerald-900' : 'bg-white/10 text-white hover:bg-white/20'"
                            class="flex-1 py-2 px-4 rounded-xl text-sm font-bold transition-all">
                            📋 Confirmação
                        </button>
                        <button @click="activeTab = 'convite'"
                            :class="activeTab === 'convite' ? 'bg-white text-emerald-900' : 'bg-white/10 text-white hover:bg-white/20'"
                            class="flex-1 py-2 px-4 rounded-xl text-sm font-bold transition-all">
                            🙏 Convite
                        </button>
                    </div>
                </div>

                <!-- Message Preview -->
                <div class="p-6 space-y-4">
                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10 text-emerald-50 text-sm leading-relaxed whitespace-pre-wrap min-h-[10rem] max-h-64 overflow-y-auto"
                        x-text="currentMessage">
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="copyMessage()"
                            class="flex items-center justify-center gap-2 py-3 bg-white text-emerald-900 font-bold rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95">
                            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <svg x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                        </button>

                        <button @click="openWhatsApp()"
                            class="flex items-center justify-center gap-2 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 2c-5.506 0-9.969 4.463-9.969 9.969 0 1.763.457 3.42 1.257 4.866l-1.334 4.866 4.968-1.303c1.403.766 3.003 1.203 4.706 1.203 5.506 0 10.151-4.463 10.151-10.151 0-5.506-4.646-9.969-10.151-9.969zm5.95 13.911c-.244.686-1.42 1.282-1.956 1.344-.537.062-1.031.282-3.414-.703-2.868-1.187-4.681-4.094-4.825-4.288-.144-.194-1.169-1.556-1.169-2.969 0-1.413.744-2.106 1.013-2.4.269-.294.625-.369.831-.369.213 0 .425.006.606.013.194.006.45-.075.706.544.263.631.894 2.181.975 2.338.081.156.138.344.031.544-.406.819-.594.981-1.131 1.631-.131.156-.269.325-.112.594.156.269.694 1.144 1.488 1.85 1.025.913 1.888 1.194 2.156 1.331.269.138.425.113.581-.069.156-.181.669-.781.85-.1.181.681-.594.344-1.144.5 1.144.156 1.819-.006 2.131-.244.313-.244.669-.744.913-1.431z" />
                            </svg>
                            Abrir WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection