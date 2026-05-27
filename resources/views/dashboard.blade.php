@extends('layouts.app')

@section('title', 'Painel Gerencial')

@section('styles')
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .status-pulse {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .status-pulse.online {
        background-color: #2ca58d;
        box-shadow: 0 0 0 0 rgba(44, 165, 141, 0.7);
        animation: pulse-green 2s infinite;
    }
    .status-pulse.offline {
        background-color: #f15bb5;
        box-shadow: 0 0 0 0 rgba(241, 91, 181, 0.7);
        animation: pulse-red 2s infinite;
    }
    .status-pulse.sem-servidor {
        background-color: #6c757d;
    }

    @keyframes pulse-green {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(44, 165, 141, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(44, 165, 141, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(44, 165, 141, 0);
        }
    }
    @keyframes pulse-red {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(241, 91, 181, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(241, 91, 181, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(241, 91, 181, 0);
        }
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #f0f0f0;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Header Control Bar / Filter -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm mb-0">
            <div class="card-body py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 bg-light-secondary rounded">
                <div>
                    <h5 class="mb-1 text-dark">
                        @if(isset($regional))
                            <i class="ti ti-map-pin me-2 text-primary"></i>Dashboard Regional: <span class="fw-bold">{{ $regional->adm_regional }}</span>
                        @else
                            <i class="ti ti-building me-2 text-primary"></i>Dashboard Local: <span class="fw-bold">{{ $activeLocal->nome ?? 'SIBEM Web' }}</span>
                        @endif
                    </h5>
                    <p class="text-muted mb-0 style-small">
                        Exercício de controle patrimonial ativo
                    </p>
                </div>
                
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2" id="filter-form">
                    @if(Auth::user()->isAdminSistema())
                        <div style="min-width: 220px;">
                            <select name="selected_regional_id" class="form-select" onchange="document.getElementById('filter-form').submit()">
                                <option value="">-- Ver Painel Central --</option>
                                @foreach($availableRegionais as $regItem)
                                    <option value="{{ $regItem->id }}" {{ $selectedRegionalId == $regItem->id ? 'selected' : '' }}>
                                        {{ $regItem->adm_regional }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <div style="width: 120px;">
                        <select name="ano" class="form-select" onchange="document.getElementById('filter-form').submit()">
                            @foreach($anosDisponiveis as $yr)
                                <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                    Ano {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(isset($regional))
    <!-- ============================================================== -->
    <!-- VIEW: REGIONAL ADMINISTRATOR                                   -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- Stats Card Grid -->
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-grd-primary text-white border-0 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Locais na Regional</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ $stats['locais'] }}</h2>
                        </div>
                        <div class="avatar bg-white-20 rounded-3 text-white p-2">
                            <i class="ti ti-building" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-grd-info text-white border-0 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total de Igrejas (CCB)</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ $stats['igrejas'] }}</h2>
                        </div>
                        <div class="avatar bg-white-20 rounded-3 text-white p-2">
                            <i class="ti ti-building-community" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-grd-success text-white border-0 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Inventários Concluídos ({{ $selectedYear }})</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ $stats['inventarios_concluidos'] }}</h2>
                        </div>
                        <div class="avatar bg-white-20 rounded-3 text-white p-2">
                            <i class="ti ti-clipboard-check" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-grd-warning text-white border-0 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Inventários em Aberto ({{ $selectedYear }})</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ $stats['inventarios_abertos'] }}</h2>
                        </div>
                        <div class="avatar bg-white-20 rounded-3 text-white p-2">
                            <i class="ti ti-clipboard-list" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Visual Comparison Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-4 px-4">
                    <h5 class="mb-0 text-dark"><i class="ti ti-chart-bar me-2 text-primary"></i>Inventários Concluídos por Administração</h5>
                </div>
                <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center" style="min-height: 300px;">
                    @if(collect($regLocaisStats)->sum('inventarios_concluidos') == 0)
                        <div class="text-center py-5">
                            <i class="ti ti-chart-bar text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">Nenhum inventário concluído no ano de {{ $selectedYear }}.</p>
                        </div>
                    @else
                        <div class="w-100" style="position: relative; height: 100%; min-height: 280px;">
                            <canvas id="regionalChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table Breakdown for the Regional -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="mb-0 text-dark"><i class="ti ti-list me-2 text-primary"></i>Panorama das Administrações Locais</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Administração Local</th>
                                    <th class="text-center">Igrejas</th>
                                    <th class="text-center">Inventários</th>
                                    <th>Progresso Geral</th>
                                    <th class="text-center">Banco</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regLocaisStats as $stat)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark d-block">{{ $stat->adm_local }}</span>
                                            <small class="text-muted">{{ $stat->cidade }} / {{ $stat->uf }}</small>
                                        </td>
                                        <td class="text-center"><span class="badge bg-light-primary text-primary fw-bold">{{ $stat->igrejas_count }}</span></td>
                                        <td class="text-center">
                                            <span class="badge bg-light-success text-success fw-bold" title="Concluídos">{{ $stat->inventarios_concluidos }}</span>
                                            @if($stat->inventarios_pendentes > 0)
                                                <span class="badge bg-light-warning text-warning fw-bold" title="Pendentes">{{ $stat->inventarios_pendentes }}</span>
                                            @endif
                                        </td>
                                        <td style="min-width: 150px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress-bar-custom flex-grow-1">
                                                    @php
                                                        $barColor = '#f15bb5'; // Default low
                                                        if($stat->progresso >= 100) $barColor = '#2ca58d';
                                                        elseif($stat->progresso >= 50) $barColor = '#00b4d8';
                                                        elseif($stat->progresso >= 25) $barColor = '#ffb703';
                                                    @endphp
                                                    <div class="progress-bar-fill" style="width: {{ $stat->progresso }}%; background-color: {{ $barColor }};"></div>
                                                </div>
                                                <span class="fw-bold text-dark small">{{ $stat->progresso }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($stat->status_conexao == 'online')
                                                <span class="badge bg-light-success text-success d-inline-flex align-items-center">
                                                    <span class="status-pulse online"></span> Online
                                                </span>
                                            @elseif($stat->status_conexao == 'offline')
                                                <span class="badge bg-light-danger text-danger d-inline-flex align-items-center" title="Erro ao conectar no banco de dados local">
                                                    <span class="status-pulse offline"></span> Inacessível
                                                </span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary d-inline-flex align-items-center" title="Nenhum servidor ativo cadastrado para este local">
                                                    <span class="status-pulse sem-servidor"></span> Sem Config.
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@else
    <!-- ============================================================== -->
    <!-- VIEW: LOCAL ADMINISTRATOR / STANDARD                          -->
    <!-- ============================================================== -->
    <div class="row">
        <!-- Stats Card Grid -->
        @if(Auth::user()->isAdminSistema())
            <!-- System Admin Main Counters -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-grd-primary text-white border-0 shadow-sm card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Regionais</h6>
                                <h2 class="mb-0 text-white fw-bold">{{ $stats['regionais'] }}</h2>
                            </div>
                            <div class="avatar bg-white-20 rounded-3 text-white p-2">
                                <i class="ti ti-map-pin" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-grd-info text-white border-0 shadow-sm card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Locais (Tenants)</h6>
                                <h2 class="mb-0 text-white fw-bold">{{ $stats['locais'] }}</h2>
                            </div>
                            <div class="avatar bg-white-20 rounded-3 text-white p-2">
                                <i class="ti ti-building" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Standard Local stats -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-grd-info text-white border-0 shadow-sm card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Igrejas Associadas</h6>
                                <h2 class="mb-0 text-white fw-bold">{{ $stats['igrejas'] }}</h2>
                            </div>
                            <div class="avatar bg-white-20 rounded-3 text-white p-2">
                                <i class="ti ti-building" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-grd-primary text-white border-0 shadow-sm card-hover">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Usuários Ativos</h6>
                                <h2 class="mb-0 text-white fw-bold">{{ $stats['usuarios'] }}</h2>
                            </div>
                            <div class="avatar bg-white-20 rounded-3 text-white p-2">
                                <i class="ti ti-users" style="font-size: 28px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-grd-success text-white border-0 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Inventários Concluídos ({{ $selectedYear }})</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ $stats['inventarios_concluidos'] }}</h2>
                        </div>
                        <div class="avatar bg-white-20 rounded-3 text-white p-2">
                            <i class="ti ti-clipboard-check" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card bg-grd-warning text-white border-0 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Inventários em Aberto ({{ $selectedYear }})</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ $stats['inventarios_abertos'] }}</h2>
                        </div>
                        <div class="avatar bg-white-20 rounded-3 text-white p-2">
                            <i class="ti ti-clipboard-list" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sector Breakdown for active local -->
        @if(!$setoresStats->isEmpty())
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="mb-0 text-dark"><i class="ti ti-chart-bar me-2 text-primary"></i>Progresso Patrimonial por Setor</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cód. / Setor</th>
                                        <th class="text-center">Igrejas no Setor</th>
                                        <th class="text-center">Inventários Concluídos</th>
                                        <th class="text-center">Inventários Pendentes</th>
                                        <th>Progresso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($setoresStats as $setStat)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-dark d-block">Setor {{ $setStat->cod_setor }}</span>
                                                <small class="text-muted">{{ $setStat->descricao }}</small>
                                            </td>
                                            <td class="text-center"><span class="badge bg-light-info text-info fw-bold">{{ $setStat->igrejas_count }}</span></td>
                                            <td class="text-center"><span class="badge bg-light-success text-success fw-bold">{{ $setStat->inventarios_concluidos }}</span></td>
                                            <td class="text-center">
                                                @if($setStat->inventarios_pendentes > 0)
                                                    <span class="badge bg-light-warning text-warning fw-bold">{{ $setStat->inventarios_pendentes }}</span>
                                                @else
                                                    <span class="text-muted small">Nenhum</span>
                                                @endif
                                            </td>
                                            <td style="min-width: 150px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress-bar-custom flex-grow-1">
                                                        @php
                                                            $barColor = '#f15bb5';
                                                            if($setStat->progresso >= 100) $barColor = '#2ca58d';
                                                            elseif($setStat->progresso >= 50) $barColor = '#00b4d8';
                                                            elseif($setStat->progresso >= 25) $barColor = '#ffb703';
                                                        @endphp
                                                        <div class="progress-bar-fill" style="width: {{ $setStat->progresso }}%; background-color: {{ $barColor }};"></div>
                                                    </div>
                                                    <span class="fw-bold text-dark small">{{ $setStat->progresso }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Token Requests or Extra Info -->
        <div class="col-lg-{{ $setoresStats->isEmpty() ? '12' : '4' }} mb-4">
            @if(Auth::user()->isAdminSistema())
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-4 px-4">
                        <h5 class="mb-0 text-dark"><i class="ti ti-key-off me-2 text-primary"></i>Solicitações de Token Pendentes</h5>
                        <a href="{{ route('admin.token-requests.index') }}" class="btn btn-sm btn-link">Ver todas</a>
                    </div>
                    <div class="card-body p-0">
                        @if($recentTokenRequests->isEmpty())
                            <div class="text-center py-5">
                                <i class="ti ti-shield-check text-success" style="font-size: 48px;"></i>
                                <p class="text-muted mt-2 mb-0">Tudo em ordem! Nenhuma solicitação pendente.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <tbody>
                                        @foreach($recentTokenRequests as $req)
                                            <tr>
                                                <td class="ps-4">
                                                    <span class="fw-bold text-dark d-block">{{ $req->user->name ?? 'N/A' }}</span>
                                                    <small class="text-muted">{{ $req->user->email ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-warning text-warning fw-bold">Pendente</span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <a href="{{ route('admin.token-requests.index') }}" class="btn btn-icon btn-sm btn-light-primary">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Informative panel for Local admin -->
                <div class="card border-0 shadow-sm bg-light-secondary h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="text-dark mb-3"><i class="ti ti-info-circle me-2 text-primary"></i>Dica do Painel</h5>
                            <p class="text-muted mb-3" style="line-height: 1.5;">
                                A tabela ao lado reflete o progresso do inventário dos templos de cada setor no ano selecionado.
                            </p>
                            <p class="text-muted mb-0" style="line-height: 1.5;">
                                Para lançar novos inventários, visualizar relatórios ou baixar PDFs conclusivos, utilize o menu lateral navegando até <strong>Inventários</strong>.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-top border-light-dark">
                            <span class="fw-bold text-dark small d-block">Suporte ao SIBEM:</span>
                            <span class="text-muted small">Contate a sua Administração Regional para dúvidas estruturais.</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection

@section('scripts')
@if(isset($regional) && collect($regLocaisStats)->sum('inventarios_concluidos') > 0)
    <!-- Dynamic Chart Rendering for Regional Panel -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('regionalChart').getContext('2d');
            
            // Get values from PHP data
            const labels = {!! json_encode(collect($regLocaisStats)->pluck('adm_local')->toArray()) !!};
            const completedData = {!! json_encode(collect($regLocaisStats)->pluck('inventarios_concluidos')->toArray()) !!};
            const pendingData = {!! json_encode(collect($regLocaisStats)->pluck('inventarios_pendentes')->toArray()) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Inventários Concluídos',
                            data: completedData,
                            backgroundColor: '#2ca58d', // Emerald Green
                            borderRadius: 4,
                            borderSkipped: false
                        },
                        {
                            label: 'Inventários Pendentes',
                            data: pendingData,
                            backgroundColor: '#ffb703', // Warm Orange
                            borderRadius: 4,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 6,
                                font: {
                                    family: "'Open Sans', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            padding: 12,
                            cornerRadius: 8,
                            bodyFont: {
                                family: "'Open Sans', sans-serif"
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Open Sans', sans-serif",
                                    size: 10
                                }
                            }
                        },
                        y: {
                            stacked: true,
                            grid: {
                                color: '#f0f0f0'
                            },
                            ticks: {
                                font: {
                                    family: "'Open Sans', sans-serif",
                                    size: 10
                                },
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif

@if(isset($recentTokenRequests) && !$recentTokenRequests->isEmpty() && session('show_login_toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Solicitações de Token Pendentes!',
                text: 'Existem solicitações de token desktop aguardando sua aprovação.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#2ca58d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ver Solicitações',
                cancelButtonText: 'Mais tarde'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('admin.token-requests.index') }}";
                }
            });
        });
    </script>
@endif
@endsection
