@extends('layouts.app')

@section('title', 'Inventários Realizados')

@section('content')
<div class="row">
    <!-- Header Page Info -->
    <div class="col-12 mb-4">
        <p class="text-muted">Análise e listagem dos inventários fechados e consolidados na Administração: <span class="fw-bold">{{ $local->nome ?? 'Nenhuma' }}</span></p>
    </div>

    <!-- Filters Column -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body py-3">
                <form action="{{ route('inventarios.concluidos') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="ano" class="form-label fw-600">Ano</label>
                        <select name="ano" id="ano" class="form-select">
                            <option value="">Todos os anos</option>
                            @foreach($anos as $anoOption)
                                <option value="{{ $anoOption }}" {{ request('ano', $selectedYear) == $anoOption ? 'selected' : '' }}>
                                    {{ $anoOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="setor" class="form-label fw-600">Setor</label>
                        <select name="setor" id="setor" class="form-select">
                            <option value="">Todos os setores</option>
                            @foreach($setores as $setorOption)
                                <option value="{{ $setorOption }}" {{ request('setor') == $setorOption ? 'selected' : '' }}>
                                    {{ $setorOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="igreja_id" class="form-label fw-600">Comum Congregação</label>
                        <select name="igreja_id" id="igreja_id" class="form-select">
                            <option value="">Todas as comuns</option>
                            @foreach($igrejas as $igrejaOption)
                                <option value="{{ $igrejaOption->codigo_ccb }}" {{ request('igreja_id') == $igrejaOption->codigo_ccb ? 'selected' : '' }}>
                                    {{ $igrejaOption->codigo_ccb }} - {{ $igrejaOption->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">
                            <i class="ti ti-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart Column -->
    <div class="col-lg-12 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-chart-bar me-2"></i>Consolidado Mensal (Ano: {{ $selectedYear }})</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="width: 100%; height: 300px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventories List Column -->
    <div class="col-lg-12 mb-4">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-list me-2"></i>Registros de Inventários</h5>
            </div>
            <div class="card-body p-0">
                @if($inventarios->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-clipboard-list text-muted" style="font-size: 48px;"></i>
                        <h6 class="mt-3">Nenhum inventário realizado encontrado</h6>
                        <p class="text-muted text-sm">Altere os filtros acima para pesquisar outros registros.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Inventário Nº</th>
                                    <th>Comum</th>
                                    <th>SIGA</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventarios as $inv)
                                    <tr>
                                        <td><span class="fw-bold">{{ $inv->codigo_unico }}</span></td>
                                         <td>
                                            <span class="fw-bold">{{ $inv->igreja->nome ?? 'Não identificada' }}</span>
                                            <small class="text-muted d-block">{{ $inv->data ? date('d/m/Y H:i', strtotime($inv->data)) : 'N/A' }}@if(isset($inv->igreja->setor)) | Setor: {{ $inv->igreja->setor }} @endif</small>
                                        </td>
                                        <td>
                                            @if($inv->siga_ok) 
                                                <span class="badge bg-light-success text-success" title="Atualizado"><i class="ti ti-check me-1"></i> Atualizado</span>
                                            @else 
                                                <span class="badge bg-light-danger text-danger" title="Pendente"><i class="ti ti-x me-1"></i> Pendente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->status === 'aberto')
                                                <span class="badge bg-light-warning text-warning">Aberto</span>
                                            @elseif($inv->status === 'fechado')
                                                <span class="badge bg-light-success text-success">Fechado</span>
                                            @else
                                                <span class="badge bg-light-primary text-primary">Auditado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer d-flex justify-content-end bg-transparent border-top-0">
                        {{ $inventarios->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        // Dynamic labels and values passed from the Laravel controller
        const labels = {!! json_encode($chartLabels) !!};
        const data = {!! json_encode($chartValues) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inventários Finalizados',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection
