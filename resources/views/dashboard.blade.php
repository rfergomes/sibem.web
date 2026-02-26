@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Filters (Admin Only) -->
    @if(isset($stats['ano']) && auth()->user()->perfil->slug !== 'operador')
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 animate-fadeIn">
            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ano</label>
                    <select name="ano" class="rounded-lg border-gray-300 text-sm focus:ring-ccb-blue-500 focus:border-ccb-blue-500">
                        @foreach(range(date('Y'), 2024) as $year)
                            <option value="{{ $year }}" {{ $year == $stats['ano'] ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Setor</label>
                    <select name="setor_id"
                        class="rounded-lg border-gray-300 text-sm focus:ring-ccb-blue-500 focus:border-ccb-blue-500">
                        <option value="">Todos</option>
                        @if(isset($sectors))
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->id }}" {{ ($stats['chart_setor'][$sector->nome] ?? null) || request('setor_id') == $sector->id ? 'selected' : '' }}>
                                    {{ $sector->nome }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <button type="submit"
                    class="bg-ccb-blue-600 hover:bg-ccb-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                    Filtrar
                </button>
            </form>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-fadeIn">
        <!-- Meta / Total Igrejas -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Meta Anual (Igrejas)</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['meta_anual'] ?? 0 }}</p>
            <div class="mt-2 text-xs font-medium text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded-full">
                {{ $stats['igrejas_inativas'] ?? 0 }} Inativas
            </div>
        </div>

        <!-- Realizados -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Realizados</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['inventarios_realizados'] ?? 0 }}</p>
            <div class="mt-2 text-xs font-medium text-green-600 bg-green-50 w-fit px-2 py-1 rounded-full">
                {{ $stats['progresso'] ?? 0 }}% Concluído
            </div>
        </div>

        <!-- Pendentes -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">A Realizar</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['pendentes'] ?? 0 }}</p>
            <div class="mt-2 text-xs font-medium text-orange-600 bg-orange-50 w-fit px-2 py-1 rounded-full">
                Prioridade
            </div>
        </div>

        <!-- Chart: Progresso -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-center items-center">
            <div class="text-center">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Progresso Global</p>
                <div class="relative w-24 h-24">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent"
                            class="text-gray-100" />
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent"
                            class="text-blue-600" stroke-dasharray="251.2"
                            stroke-dashoffset="{{ 251.2 - (251.2 * ($stats['progresso'] ?? 0) / 100) }}" />
                    </svg>
                    <span
                        class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-xl font-bold text-ccb-blue">
                        {{ $stats['progresso'] ?? 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 animate-fadeIn">
        <!-- By Sector -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 uppercase mb-4">Inventários por Setor</h3>
            <div class="h-64">
                <canvas id="chartSetor"></canvas>
            </div>
        </div>

        <!-- By Month -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 uppercase mb-4">Evolução Mensal</h3>
            <div class="h-64">
                <canvas id="chartMensal"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sector Chart
            const ctxSetor = document.getElementById('chartSetor').getContext('2d');
            new Chart(ctxSetor, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($stats['chart_setor'] ?? [])) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($stats['chart_setor'] ?? [])) !!},
                        backgroundColor: [
                            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });

            // Monthly Chart
            const ctxMensal = document.getElementById('chartMensal').getContext('2d');
            const monthlyData = {!! json_encode($stats['chart_mensal'] ?? []) !!};
            const labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

            new Chart(ctxMensal, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Realizados',
                        data: monthlyData,
                        backgroundColor: '#4F46E5',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        });
    </script>

    <!-- Main Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-800">Próximos Inventários</h2>
            <a href="{{ route('appointments.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">Ver
                Agenda Completa</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Localidade</th>
                        <th class="px-4 py-3">Data Prevista</th>
                        <th class="px-4 py-3">Responsável</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 rounded-r-lg">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($nextAppointments as $appt)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $appt->local->nome }}</td>
                            <td class="px-4 py-3">
                                {{ $appt->scheduled_at ? $appt->scheduled_at->format('d/m/Y H:i') : 'A definir' }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="font-bold">{{ $appt->responsavel_nome }}</span><br>
                                <span class="text-gray-400 uppercase tracking-tighter">{{ $appt->responsavel_cargo }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $appt->status === 'confirmado' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $appt->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('appointments.show', $appt) }}"
                                    class="text-blue-600 font-bold hover:underline">Detalhes</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">Nenhum agendamento para os
                                próximos dias.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Versículo do Dia (Gemini AI) -->
    @if(isset($dailyData))
        <div x-data="{ showDevotional: false }" class="mt-8">
            <div @click="showDevotional = true" class="fixed bottom-14 right-6 cursor-pointer group animate-slideUp"
                style="position: fixed; bottom: 3.5rem; right: 1.5rem; animation: slideUp 0.5s ease-out;">
                <div
                    class="bg-white rounded-xl shadow-xl p-5 border border-blue-100 max-w-xs relative hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute -top-2 left-4 w-4 h-4 bg-white border-t border-l border-blue-100 rotate-45"></div>

                    <div class="flex items-center gap-3 mb-2">
                        <div class="bg-ccb-blue-50 p-1.5 rounded-lg">
                            <svg class="w-4 h-4 text-ccb-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.082.477 4 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.082.477-4 1.253" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[13px] italic text-gray-700 leading-relaxed line-clamp-2">
                                "{{ $dailyData['verse'] }}"
                            </p>
                            <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wide">
                                {{ $dailyData['reference'] ?? '' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                        <span class="text-[9px] font-bold text-ccb-blue-400 uppercase tracking-widest">Clique para meditar</span>
                        <p class="text-[10px] font-bold text-ccb-blue-900">
                            Versículo do Dia
                        </p>
                    </div>
                </div>
            </div>

            <!-- Devotional Modal -->
            <div x-show="showDevotional" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                @click.away="showDevotional = false" style="display: none;">

                <div
                    class="bg-ccb-blue w-full max-w-3xl rounded-[2rem] shadow-2xl relative max-h-[85vh] flex flex-col overflow-hidden">
                    <!-- Background blobs (Contained by outer overflow-hidden) -->
                    <div
                        class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 -mb-20 -ml-20 w-64 h-64 bg-ccb-blue-400/10 rounded-full blur-3xl pointer-events-none">
                    </div>

                    <!-- Close Button -->
                    <button @click="showDevotional = false"
                        class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors z-20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Scrollable Content Area -->
                    <div class="overflow-y-auto custom-scrollbar relative z-10 p-6 sm:px-8 sm:pt-10 sm:pb-6">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="bg-white/10 p-2.5 rounded-xl">
                                <svg class="w-6 h-6 text-ccb-blue-200" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71L12 2z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-white tracking-tight">Reflexão Espiritual</h2>
                        </div>

                        <div class="space-y-6">
                            <div class="text-center py-4 px-4 bg-white/5 rounded-3xl border border-white/10 relative">
                                <div
                                    class="absolute -top-4 left-1/2 -translate-x-1/2 bg-ccb-dark px-4 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest text-ccb-blue-200 border border-white/10">
                                    Palavra de Hoje
                                </div>
                                <p class="text-lg sm:text-xl font-serif italic text-white leading-relaxed mb-3 mt-1">
                                    "{{ $dailyData['verse'] }}"
                                </p>
                                <span
                                    class="inline-block px-3 py-1 bg-ccb-blue/40 rounded-full text-xs font-bold text-ccb-blue-100 uppercase tracking-wider border border-white/20">
                                    {{ $dailyData['reference'] ?? '' }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-ccb-blue-200 text-xs font-bold uppercase tracking-widest mb-3">A Mensagem</h3>
                                <p class="text-sm leading-relaxed text-ccb-blue-50/90 font-medium text-justify">
                                    {{ $dailyData['reflection'] }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div
                                    class="p-4 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-colors">
                                    <h3
                                        class="text-ccb-blue-200 text-[10px] font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Oração
                                    </h3>
                                    <p class="text-ccb-blue-100 text-sm italic leading-relaxed">
                                        "{{ $dailyData['prayer'] }}"
                                    </p>
                                </div>

                                <div
                                    class="p-4 bg-ccb-blue-600/30 rounded-2xl border border-white/10 hover:bg-ccb-blue-600/40 transition-colors">
                                    <h3
                                        class="text-ccb-blue-200 text-[10px] font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        Aplicação Prática
                                    </h3>
                                    <p class="text-ccb-blue-100 text-sm leading-relaxed">
                                        {{ $dailyData['application'] }}
                                    </p>
                                </div>
                            </div>

                            @if(!empty($dailyData['curiosity']))
                                <div class="pt-6 border-t border-white/10">
                                    <div class="bg-amber-400/10 rounded-2xl p-5 border border-amber-400/20">
                                        <h3
                                            class="text-amber-300 text-xs font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13.536 14.95a1 1 0 011.414 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707zM16.243 16.243a1 1 0 01-1.414 0l-.707-.707a1 1 0 011.414-1.414l.707.707a1 1 0 010 1.414z" />
                                            </svg>
                                            Você sabia?
                                        </h3>
                                        <p class="text-ccb-blue-50 text-sm leading-relaxed">
                                            {{ $dailyData['curiosity'] }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="mt-4 text-center text-[10px] text-white/30 uppercase tracking-[0.2em] font-medium">
                            SIBEM • IA e Espiritualidade • {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @keyframes slideUp {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .font-serif {
                font-family: Georgia, Cambria, "Times New Roman", Times, serif;
            }
        </style>
    @endif
@endsection
