@extends('layouts.app')

@section('title', 'Agendamento de Inventários')

@section('content')
    <div class="space-y-6 animate-fadeIn" x-data="{ 
                                                                                                showJustificationModal: false, 
                                                                                                selectedAppointmentId: null, 
                                                                                                newStatus: '',
                                                                                            confirmStatus(id, status) {
                                                                                                if (status === 'cancelado' || status === 'adiado') {
                                                                                                    this.selectedAppointmentId = id;
                                                                                                    this.newStatus = status;
                                                                                                    this.showJustificationModal = true;
                                                                                                } else {
                                                                                                    window.confirmAction('Confirmar Status', 'Deseja alterar o status para ' + status + '?', () => {
                                                                                                        const form = document.createElement('form');
                                                                                                        form.method = 'POST';
                                                                                                        form.action = '/admin/agendamentos/' + id + '/status';

                                                                                                        const csrfInput = document.createElement('input');
                                                                                                        csrfInput.type = 'hidden';
                                                                                                        csrfInput.name = '_token';
                                                                                                        csrfInput.value = '{{ csrf_token() }}';
                                                                                                        form.appendChild(csrfInput);

                                                                                                        const statusInput = document.createElement('input');
                                                                                                        statusInput.type = 'hidden';
                                                                                                        statusInput.name = 'status';
                                                                                                        statusInput.value = status;
                                                                                                        form.appendChild(statusInput);

                                                                                                        document.body.appendChild(form);
                                                                                                        form.submit();
                                                                                                    });
                                                                                                }
                                                                                            },
                                                        confirmDelete(id, status, url, data, local, responsavel) {
                                                            if (status === 'confirmado') {
                                                                Swal.fire({
                                                                    title: 'Ação Bloqueada',
                                                                    text: 'Não é possível excluir um agendamento confirmado (verde). Por favor, cancele ou reagende (para voltar a previsão) antes de excluir.',
                                                                    icon: 'error',
                                                                    confirmButtonColor: '#1e40af',
                                                                    confirmButtonText: 'Entendi',
                                                                    customClass: {
                                                                        popup: 'rounded-xl',
                                                                        confirmButton: 'px-6 py-2 rounded-lg font-bold'
                                                                    }
                                                                });
                                                                return;
                                                            }

                                                            window.confirmAction('Excluir Agendamento', 
                                                                'Deseja excluir permanentemente este agendamento? Esta ação não pode ser desfeita.', 
                                                                () => {
                                                                const form = document.createElement('form');
                                                                form.method = 'POST';
                                                                form.action = url;

                                                                const methodInput = document.createElement('input');
                                                                methodInput.type = 'hidden';
                                                                methodInput.name = '_method';
                                                                methodInput.value = 'DELETE';
                                                                form.appendChild(methodInput);

                                                                const tokenMeta = document.querySelector('meta[name=\'csrf-token\']');
                                                                const csrfInput = document.createElement('input');
                                                                csrfInput.type = 'hidden';
                                                                csrfInput.name = '_token';
                                                                csrfInput.value = tokenMeta ? tokenMeta.getAttribute('content') : '';
                                                                form.appendChild(csrfInput);

                                                                document.body.appendChild(form);
                                                                form.submit();
                                                            });
                                                        }
                                                    }">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Gestão de Visitas</h2>
                <p class="text-sm text-gray-500">Acompanhe e organize as datas de inventário nas localidades.</p>
            </div>
            <a href="{{ route('appointments.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95 gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Agendamento
            </a>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('appointments.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
                <div class="w-full sm:w-32">
                    <label for="year" class="block text-xs font-bold text-gray-400 uppercase mb-2">Ano</label>
                    <select name="year" id="year" onchange="this.form.submit()"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-2.5 font-bold text-gray-700 transition-all">
                        <option value="">Todos</option>
                        @foreach(range(date('Y'), date('Y') - 5) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label for="month" class="block text-xs font-bold text-gray-400 uppercase mb-2">Mês</label>
                    <select name="month" id="month" onchange="this.form.submit()"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-2.5 font-bold text-gray-700 transition-all">
                        <option value="">Todos</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->locale('pt_BR')->monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-64">
                    <label for="status" class="block text-xs font-bold text-gray-400 uppercase mb-2">Filtrar por
                        Status</label>
                    <select name="status" id="status" onchange="this.form.submit()"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-2.5 font-bold text-gray-700 transition-all">
                        <option value="">Todos os Status</option>
                        <option value="previsao" {{ request('status') === 'previsao' ? 'selected' : '' }}>🔵 Previsão</option>
                        <option value="confirmado" {{ request('status') === 'confirmado' ? 'selected' : '' }}>🟢 Confirmado
                        </option>
                        <option value="adiado" {{ request('status') === 'adiado' ? 'selected' : '' }}>🟠 Adiado</option>
                        <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>🔴 Cancelado
                        </option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Tabela de Agendamentos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
            <div class="overflow-visible">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-4">Localidade</th>
                            <th class="px-6 py-4 hidden md:table-cell">Responsável</th>
                            <th class="px-3 sm:px-6 py-4">Data Planejada</th>
                            <th class="px-3 sm:px-6 py-4">Status</th>
                            <th class="px-6 py-4 hidden lg:table-cell">Criado por</th>
                            <th class="px-3 sm:px-6 py-4 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800 text-xs sm:text-sm">
                                            {{ $appointment->igreja ? $appointment->igreja->nome : $appointment->local->nome }}
                                        </span>
                                        @if($appointment->igreja)
                                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tight">
                                                {{ $appointment->local->nome }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 hidden md:table-cell">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-700">{{ $appointment->responsavel_nome }}</span>
                                        <span
                                            class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">{{ $appointment->responsavel_cargo ?? 'Sem Cargo' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 text-xs sm:text-sm">
                                    <div class="flex items-center gap-2">
                                        <svg class="hidden sm:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span
                                            class="font-medium text-gray-600">{{ $appointment->scheduled_at ? $appointment->scheduled_at->format('d/m/y H:i') : '---' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'previsao' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'confirmado' => 'bg-green-50 text-green-600 border-green-100',
                                            'cancelado' => 'bg-red-50 text-red-600 border-red-100',
                                            'adiado' => 'bg-orange-50 text-orange-600 border-orange-100',
                                        ];
                                    @endphp
                                    <span
                                        class="px-2 sm:px-3 py-0.5 sm:py-1 rounded-full border text-[9px] sm:text-[11px] font-bold uppercase tracking-wider {{ $statusClasses[$appointment->status] ?? 'bg-gray-50 text-gray-500 border-gray-200' }}">
                                        <span
                                            class="hidden sm:inline">{{ $statusLabels[$appointment->status] ?? 'INDEFINIDO' }}</span>
                                        <span
                                            class="sm:hidden">{{ substr($statusLabels[$appointment->status] ?? '?', 0, 1) }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 italic text-xs hidden lg:table-cell">
                                    {{ $appointment->creator->name }}
                                </td>
                                <td class="px-3 sm:px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('appointments.show', $appointment) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="WhatsApp e Detalhes">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </a>

                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="p-2 text-gray-400 hover:bg-gray-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl border border-gray-100 z-50 py-2 py-2 overflow-hidden animate-scaleIn"
                                                style="display: none;">
                                                <button @click="confirmStatus({{ $appointment->id }}, 'confirmado')"
                                                    class="w-full text-left px-4 py-2 text-xs font-bold text-green-600 hover:bg-green-50 uppercase tracking-tighter">✅
                                                    Confirmar Data</button>
                                                <button @click="confirmStatus({{ $appointment->id }}, 'adiado')"
                                                    class="w-full text-left px-4 py-2 text-xs font-bold text-orange-600 hover:bg-orange-50 uppercase tracking-tighter">📅
                                                    Reagendar</button>
                                                <button @click="confirmStatus({{ $appointment->id }}, 'cancelado')"
                                                    class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 uppercase tracking-tighter">❌
                                                    Cancelar</button>
                                                <button type="button"
                                                    @click="confirmDelete({{ $appointment->id }}, '{{ $appointment->status }}', '{{ route('appointments.destroy', $appointment->id) }}', '{{ $appointment->scheduled_at ? $appointment->scheduled_at->format('d/m/Y H:i') : 'A definir' }}', '{{ addslashes($appointment->igreja ? $appointment->igreja->nome : $appointment->local->nome) }}', '{{ addslashes($appointment->responsavel_nome) }}')"
                                                    class="w-full text-left px-4 py-2 text-xs font-bold text-gray-400 hover:bg-gray-50 hover:text-red-500 uppercase tracking-tighter transition-colors border-t border-gray-50 mt-1 pt-1">
                                                    Excluir Registro
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Nenhum agendamento
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>

        <!-- Justification/Reschedule Modal -->
        <div x-show="showJustificationModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                    @click="showJustificationModal = false"></div>

                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg animate-scaleIn p-8">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                            <span x-text="newStatus === 'cancelado' ? '❌ Cancelamento' : '📅 Reagendamento'"></span>
                        </h3>
                        <p class="text-sm text-gray-500 mt-2"
                            x-text="newStatus === 'cancelado' 
                                                                                                ? 'Por favor, informe o motivo do cancelamento.' 
                                                                                                : 'Defina a nova data para o reagendamento.'">
                        </p>
                    </div>

                    <form :action="`/admin/agendamentos/${selectedAppointmentId}/status`" method="POST">
                        @csrf
                        <input type="hidden" name="status" :value="newStatus">

                        <div class="space-y-6">
                            <!-- Date Field for Rescheduling -->
                            <template x-if="newStatus === 'adiado'">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Nova
                                        Data Prevista</label>
                                    <input type="datetime-local" name="new_date" required
                                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all">
                                </div>
                            </template>

                            <div>
                                <label
                                    class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Justificativa
                                    / Observação</label>
                                <textarea name="justification" rows="3" required
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all placeholder-gray-400"
                                    placeholder="Ex: Motivo da alteração, quem solicitou..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" @click="showJustificationModal = false"
                                class="px-5 py-2.5 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
                            <button type="submit"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-md active:scale-95">
                                Confirmar Alteração
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection