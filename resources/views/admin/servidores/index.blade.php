@extends('layouts.app')

@section('title', 'Servidores (Bancos Locais)')

@section('styles')
<style>
    .server-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    .server-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .status-bar-indicator {
        height: 5px;
        width: 100%;
        transition: background-color 0.3s ease;
    }
    .bg-success-subtle {
        background-color: rgba(40, 167, 69, 0.12) !important;
        color: #28a745 !important;
    }
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.12) !important;
        color: #dc3545 !important;
    }
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #b58105 !important;
    }
    .bg-info-subtle {
        background-color: rgba(23, 162, 184, 0.12) !important;
        color: #17a2b8 !important;
    }
    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.12) !important;
        color: #6c757d !important;
    }
    .pulse-green {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #28a745;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        animation: pulse-green-anim 1.5s infinite;
    }
    @keyframes pulse-green-anim {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between py-3" style="border-radius: 8px 8px 0 0;">
                <h4 class="mb-0 text-white d-flex align-items-center">
                    <i class="ti ti-server me-2"></i> Servidores de Banco de Dados (Tenants)
                </h4>
                
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm me-2" role="group" aria-label="Visualização">
                        <button type="button" class="btn btn-outline-info btn-view-table" title="Visualização em Tabela">
                            <i class="ti ti-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info btn-view-cards" title="Visualização em Cards">
                            <i class="ti ti-layout-grid"></i>
                        </button>
                    </div>
                    
                    <a href="{{ route('admin.servidores.create') }}" class="btn btn-info btn-sm d-flex align-items-center ms-2 px-3 fw-bold">
                        <i class="ti ti-plus me-1"></i> Novo Servidor
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Search Form -->
                <form action="{{ route('admin.servidores.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por host, banco ou localidade..." value="{{ request('search') }}" onchange="this.form.submit()">
                            <button class="btn btn-outline-secondary d-flex align-items-center" type="submit">
                                <i class="ti ti-search me-1"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.servidores.index') }}" class="btn btn-outline-danger d-flex align-items-center">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($servidores->isEmpty())
                    <div class="text-center p-5 border rounded" style="background-color: #fcfcfc;">
                        <i class="ti ti-server text-muted" style="font-size: 54px;"></i>
                        <h5 class="mt-3 fw-bold text-secondary">Nenhum servidor cadastrado ou encontrado</h5>
                        <p class="text-muted">Cadastre um novo servidor local vinculando-o à sua Administração Local.</p>
                        <a href="{{ route('admin.servidores.create') }}" class="btn btn-primary btn-sm mt-2 px-3 fw-bold">Cadastrar Servidor</a>
                    </div>
                @else
                    <!-- ============================================== -->
                    <!-- CARD DISPLAY MODE -->
                    <!-- ============================================== -->
                    <div class="view-cards">
                        <div class="row">
                            @foreach($servidores as $servidor)
                                <div class="col-md-6 col-lg-4 mb-4 server-item-container" data-server-id="{{ $servidor->id }}">
                                    <div class="card h-100 shadow-sm border-0 position-relative overflow-hidden server-card" style="border-radius: 10px;">
                                        <!-- Connection Indicator Top Bar -->
                                        <div class="status-bar-indicator bg-secondary" id="card-indicator-{{ $servidor->id }}"></div>
                                        
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-light text-dark border small" style="font-size: 10px;">
                                                        ID #{{ $servidor->id }}
                                                    </span>
                                                    <!-- Connection Badge -->
                                                    <span class="badge bg-secondary-subtle border border-secondary-subtle connection-badge" id="card-badge-{{ $servidor->id }}">
                                                        <div class="spinner-border spinner-border-sm text-secondary me-1" role="status" style="width: 10px; height: 10px; border-width: 1.5px;"></div> Verificando
                                                    </span>
                                                </div>
                                                
                                                <h5 class="card-title mb-2 fw-bold text-dark">{{ $servidor->descricao }}</h5>
                                                <p class="text-muted mb-1 small" style="line-height: 1.3;">
                                                    <i class="ti ti-building me-1 text-primary"></i>
                                                    <strong>{{ $servidor->local->adm_local ?? 'Local Não Associado' }}</strong>
                                                </p>
                                                @if($servidor->local && $servidor->local->regional)
                                                    <span class="text-muted d-block small" style="font-size: 11px;">
                                                        <i class="ti ti-map-pin me-1 text-secondary"></i>{{ $servidor->local->regional->adm_regional }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <hr class="opacity-10 my-3">
                                            
                                            <!-- Credenciais -->
                                            <div class="server-details small mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Servidor/Porta:</span>
                                                    <span class="fw-bold text-dark">{{ $servidor->servidor }}:{{ $servidor->porta }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Banco de Dados:</span>
                                                    <span class="fw-bold text-dark">{{ $servidor->banco }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Usuário:</span>
                                                    <span class="fw-bold text-dark">{{ $servidor->usuario }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Status Ativo:</span>
                                                    @if($servidor->ativo)
                                                        <span class="badge bg-success-subtle border border-success-subtle py-0 px-2">Ativo</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle border border-danger-subtle py-0 px-2">Inativo</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Estatísticas Dinâmicas do Health Check -->
                                            <div class="health-counters p-3 rounded mb-3 bg-light d-flex align-items-center justify-content-around border">
                                                <div class="text-center">
                                                    <small class="text-muted d-block fw-bold uppercase-heading mb-1" style="font-size: 10px; letter-spacing: 0.5px;">BENS MÓVEIS</small>
                                                    <span class="fs-5 fw-bold text-primary bens-counter-val" id="card-bens-{{ $servidor->id }}">
                                                        <div class="spinner-border spinner-border-sm text-primary" role="status" style="width: 12px; height: 12px; border-width: 1.5px;"></div>
                                                    </span>
                                                </div>
                                                <div class="vr bg-secondary opacity-20" style="height: 30px;"></div>
                                                <div class="text-center">
                                                    <small class="text-muted d-block fw-bold uppercase-heading mb-1" style="font-size: 10px; letter-spacing: 0.5px;">INVENTÁRIOS</small>
                                                    <span class="fs-5 fw-bold text-success inventarios-counter-val" id="card-inv-{{ $servidor->id }}">
                                                        <div class="spinner-border spinner-border-sm text-success" role="status" style="width: 12px; height: 12px; border-width: 1.5px;"></div>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Status de Provisionamento -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Provisionamento:</span>
                                                <div class="provision-badge-wrapper" id="card-provision-wrapper-{{ $servidor->id }}">
                                                    @if($servidor->provisionado)
                                                        <span class="badge bg-success-subtle border border-success-subtle py-1 px-2" title="Provisionado em: {{ $servidor->data_provisionamento ? $servidor->data_provisionamento->format('d/m/Y H:i') : 'N/A' }}">
                                                            <i class="ti ti-check me-1"></i> Provisionado
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle border border-warning-subtle py-1 px-2">
                                                            <i class="ti ti-alert-triangle me-1"></i> Não Provisionado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Footer Actions -->
                                        <div class="card-footer bg-light border-top-0 d-flex justify-content-between align-items-center p-3">
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-secondary btn-test-connection" data-id="{{ $servidor->id }}" title="Testar Conectividade">
                                                    <i class="ti ti-refresh"></i>
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-icon btn-light-info btn-provision {{ $servidor->provisionado ? 'd-none' : '' }}" data-id="{{ $servidor->id }}" id="card-provision-btn-{{ $servidor->id }}" title="Provisionar Tabelas">
                                                    <i class="ti ti-database-import"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.servidores.edit', $servidor->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Credenciais">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.servidores.destroy', $servidor->id) }}" method="POST" class="d-inline delete-servidor-form {{ $servidor->provisionado ? 'd-none' : '' }}" id="card-delete-form-{{ $servidor->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Configuração">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- TABLE DISPLAY MODE -->
                    <!-- ============================================== -->
                    <div class="view-table">
                        <div class="table-responsive border rounded">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 70px;">ID</th>
                                        <th>Descrição / Administração Local</th>
                                        <th>Configuração de Conexão</th>
                                        <th class="text-center" style="width: 140px;">Conectividade</th>
                                        <th class="text-center" style="width: 100px;">Bens</th>
                                        <th class="text-center" style="width: 100px;">Inventários</th>
                                        <th class="text-center" style="width: 160px;">Provisionamento</th>
                                        <th class="text-center" style="width: 200px;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servidores as $servidor)
                                        <tr class="server-item-container" data-server-id="{{ $servidor->id }}">
                                            <td>
                                                <span class="fw-bold text-muted">#{{ $servidor->id }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark d-block">{{ $servidor->descricao }}</span>
                                                <span class="text-muted small">
                                                    <i class="ti ti-building me-1"></i>{{ $servidor->local->adm_local ?? 'Local Não Associado' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light-primary text-primary font-monospace">{{ $servidor->servidor }}:{{ $servidor->porta }}</span>
                                                <span class="d-block small text-muted font-monospace mt-1">BD: {{ $servidor->banco }} | User: {{ $servidor->usuario }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary-subtle border border-secondary-subtle connection-badge" id="table-badge-{{ $servidor->id }}">
                                                    <div class="spinner-border spinner-border-sm text-secondary me-1" role="status" style="width: 10px; height: 10px; border-width: 1.5px;"></div> Verificando
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold text-primary" id="table-bens-{{ $servidor->id }}">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status" style="width: 11px; height: 11px; border-width: 1.5px;"></div>
                                            </td>
                                            <td class="text-center fw-bold text-success" id="table-inv-{{ $servidor->id }}">
                                                <div class="spinner-border spinner-border-sm text-success" role="status" style="width: 11px; height: 11px; border-width: 1.5px;"></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="provision-badge-wrapper" id="table-provision-wrapper-{{ $servidor->id }}">
                                                    @if($servidor->provisionado)
                                                        <span class="badge bg-success-subtle border border-success-subtle py-1 px-2" title="Provisionado em: {{ $servidor->data_provisionamento ? $servidor->data_provisionamento->format('d/m/Y H:i') : 'N/A' }}">
                                                            <i class="ti ti-check me-1"></i> Provisionado
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning-subtle border border-warning-subtle py-1 px-2">
                                                            <i class="ti ti-alert-triangle me-1"></i> Não Provisionado
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-secondary btn-test-connection" data-id="{{ $servidor->id }}" title="Testar Conectividade">
                                                        <i class="ti ti-refresh"></i>
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-sm btn-icon btn-light-info btn-provision {{ $servidor->provisionado ? 'd-none' : '' }}" data-id="{{ $servidor->id }}" id="table-provision-btn-{{ $servidor->id }}" title="Provisionar Tabelas">
                                                        <i class="ti ti-database-import"></i>
                                                    </button>
                                                    
                                                    <a href="{{ route('admin.servidores.edit', $servidor->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Credenciais">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    
                                                    <form action="{{ route('admin.servidores.destroy', $servidor->id) }}" method="POST" class="d-inline delete-servidor-form {{ $servidor->provisionado ? 'd-none' : '' }}" id="table-delete-form-{{ $servidor->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Configuração">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer d-flex justify-content-end border-top-0 bg-transparent mt-3 p-0">
                        {{ $servidores->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {


        // ----------------------------------------------------
        // ASYNC HEALTH CHECK & AUTO-SYNC ENGINE
        // ----------------------------------------------------
        const servers = document.querySelectorAll('.server-item-container[data-server-id]');
        
        servers.forEach(serverEl => {
            const serverId = serverEl.getAttribute('data-server-id');
            // Select elements for both Card and Table views
            const cardIndicator = document.getElementById(`card-indicator-${serverId}`);
            const cardBadge = document.getElementById(`card-badge-${serverId}`);
            const tableBadge = document.getElementById(`table-badge-${serverId}`);
            
            const cardBens = document.getElementById(`card-bens-${serverId}`);
            const tableBens = document.getElementById(`table-bens-${serverId}`);
            
            const cardInv = document.getElementById(`card-inv-${serverId}`);
            const tableInv = document.getElementById(`table-inv-${serverId}`);

            const cardProvisionWrapper = document.getElementById(`card-provision-wrapper-${serverId}`);
            const tableProvisionWrapper = document.getElementById(`table-provision-wrapper-${serverId}`);
            const cardProvisionBtn = document.getElementById(`card-provision-btn-${serverId}`);
            const tableProvisionBtn = document.getElementById(`table-provision-btn-${serverId}`);
            const cardDeleteForm = document.getElementById(`card-delete-form-${serverId}`);
            const tableDeleteForm = document.getElementById(`table-delete-form-${serverId}`);

            // Perform single health check AJAX call
            fetch(`{{ url('admin/servidores') }}/${serverId}/test-connection`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update connection badges
                    const successBadgeHtml = `<span class="d-inline-flex align-items-center"><span class="pulse-green me-1"></span> Online (${data.latency}ms)</span>`;
                    if (cardBadge) {
                        cardBadge.className = 'badge bg-success-subtle border border-success-subtle connection-badge';
                        cardBadge.innerHTML = successBadgeHtml;
                    }
                    if (tableBadge) {
                        tableBadge.className = 'badge bg-success-subtle border border-success-subtle connection-badge';
                        tableBadge.innerHTML = successBadgeHtml;
                    }
                    if (cardIndicator) {
                        cardIndicator.className = 'status-bar-indicator bg-success';
                    }

                    // Update counters
                    if (cardBens) cardBens.textContent = data.bens_count;
                    if (tableBens) tableBens.textContent = data.bens_count;
                    if (cardInv) cardInv.textContent = data.inventarios_count;
                    if (tableInv) tableInv.textContent = data.inventarios_count;

                    // If physical tables detected as provisioned, auto-sync and update frontend badges
                    if (data.is_provisioned_fisicamente) {
                        const provisionedBadgeHtml = `
                            <span class="badge bg-success-subtle border border-success-subtle py-1 px-2" title="Tabelas físicas encontradas no banco local">
                                <i class="ti ti-check me-1"></i> Provisionado
                            </span>
                        `;
                        if (cardProvisionWrapper) cardProvisionWrapper.innerHTML = provisionedBadgeHtml;
                        if (tableProvisionWrapper) tableProvisionWrapper.innerHTML = provisionedBadgeHtml;
                        
                        // Hide provision buttons and delete forms
                        if (cardProvisionBtn) cardProvisionBtn.classList.add('d-none');
                        if (tableProvisionBtn) tableProvisionBtn.classList.add('d-none');
                        if (cardDeleteForm) cardDeleteForm.classList.add('d-none');
                        if (tableDeleteForm) tableDeleteForm.classList.add('d-none');
                    }
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(err => {
                // Update connection badges to offline
                const errorBadgeHtml = `<i class="ti ti-alert-triangle me-1"></i> Offline`;
                const errorTooltip = err.message || 'Erro desconhecido';
                
                if (cardBadge) {
                    cardBadge.className = 'badge bg-danger-subtle border border-danger-subtle connection-badge';
                    cardBadge.innerHTML = errorBadgeHtml;
                    cardBadge.setAttribute('title', errorTooltip);
                }
                if (tableBadge) {
                    tableBadge.className = 'badge bg-danger-subtle border border-danger-subtle connection-badge';
                    tableBadge.innerHTML = errorBadgeHtml;
                    tableBadge.setAttribute('title', errorTooltip);
                }
                if (cardIndicator) {
                    cardIndicator.className = 'status-bar-indicator bg-danger';
                }

                // Update counters to unavailable
                if (cardBens) cardBens.innerHTML = '<span class="text-danger" title="Indisponível">--</span>';
                if (tableBens) tableBens.innerHTML = '<span class="text-danger" title="Indisponível">--</span>';
                if (cardInv) cardInv.innerHTML = '<span class="text-danger" title="Indisponível">--</span>';
                if (tableInv) tableInv.innerHTML = '<span class="text-danger" title="Indisponível">--</span>';
            });
        });

        // ----------------------------------------------------
        // MANUAL CONNECTION TEST
        // ----------------------------------------------------
        document.querySelectorAll('.btn-test-connection').forEach(btn => {
            btn.addEventListener('click', function () {
                const serverId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Testando Conexão...',
                    text: 'Aguarde enquanto tentamos nos comunicar com o servidor MySQL remoto.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`{{ url('admin/servidores') }}/${serverId}/test-connection`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        Swal.fire({
                            title: 'Conexão Estabelecida!',
                            html: `
                                <div class="text-start">
                                    <p class="mb-2 text-success fw-bold"><i class="ti ti-check-circle me-1"></i> Conectado com sucesso!</p>
                                    <ul class="list-unstyled mb-0 small">
                                        <li><strong>Latência:</strong> ${data.latency}ms</li>
                                        <li><strong>Tabelas Provisionadas fisicamente:</strong> ${data.is_provisioned_fisicamente ? 'Sim' : 'Não'}</li>
                                        <li><strong>Bens cadastrados localmente:</strong> ${data.bens_count}</li>
                                        <li><strong>Inventários realizados:</strong> ${data.inventarios_count}</li>
                                    </ul>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonColor: '#033D60'
                        }).then(() => {
                            window.location.reload(); // reload to sync page state
                        });
                    } else {
                        Swal.fire({
                            title: 'Falha de Conectividade',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#033D60'
                        });
                    }
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire({
                        title: 'Erro no Servidor',
                        text: 'Ocorreu um erro interno de rede ao tentar processar o teste.',
                        icon: 'error',
                        confirmButtonColor: '#033D60'
                    });
                });
            });
        });

        // ----------------------------------------------------
        // DATABASE PROVISIONING
        // ----------------------------------------------------
        document.querySelectorAll('.btn-provision').forEach(btn => {
            btn.addEventListener('click', function () {
                const serverId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Provisionar Banco de Dados?',
                    text: 'Esta ação criará as tabelas estruturais de inventários localmente neste servidor. Esta operação não pode ser desfeita e sobrescritas são bloqueadas para evitar perda de dados.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, provisionar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Provisionando...',
                            text: 'Criando tabelas e chaves de indexação. Aguarde...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`{{ url('admin/servidores') }}/${serverId}/provision`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                let logLines = '';
                                if (data.logs && data.logs.length) {
                                    logLines = '<div class="bg-dark text-light p-3 rounded font-monospace text-start small mt-2" style="max-height: 200px; overflow-y: auto;">';
                                    data.logs.forEach(log => {
                                        logLines += `<div>${log}</div>`;
                                    });
                                    logLines += '</div>';
                                }
                                
                                Swal.fire({
                                    title: 'Provisionado com Sucesso!',
                                    html: `
                                        <div>
                                            <p class="text-success fw-bold"><i class="ti ti-checkbox me-1"></i> As tabelas foram criadas com sucesso no banco de dados remoto.</p>
                                            ${logLines}
                                        </div>
                                    `,
                                    icon: 'success',
                                    confirmButtonColor: '#033D60'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Falha no Provisionamento',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#033D60'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            Swal.fire({
                                title: 'Erro de Provisionamento',
                                text: 'Ocorreu um erro ao enviar a solicitação para o servidor.',
                                icon: 'error',
                                confirmButtonColor: '#033D60'
                            });
                        });
                    }
                });
            });
        });

        // ----------------------------------------------------
        // DELETE SERVER CONFIRMATION
        // ----------------------------------------------------
        document.querySelectorAll('.delete-servidor-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Excluir Configuração?',
                    text: "Esta ação apagará apenas os dados de credenciais no banco central. O banco remoto não será afetado. Esta ação só é permitida em servidores não provisionados.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
