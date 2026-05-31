@extends('layouts.app')

@section('title', 'Cronograma de Visitas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-calendar-event me-2"></i>Cronograma de Visitas (Inventários)</h4>
                @if(!Auth::user()->isAuditor())
                    <button class="btn btn-outline-info btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalNovoAgendamento">
                        <i class="ti ti-plus me-1"></i> Novo Agendamento
                    </button>
                @endif
            </div>
            
            <div class="card-body">
                <!-- Filters Form -->
                <form action="{{ route('agendamentos.index') }}" method="GET" class="row g-3 mb-4" id="filter-form">
                    <div class="col-md-3 col-lg-4">
                        <label class="form-label fw-bold">Busca Textual</label>
                        <input type="text" name="search" class="form-control" placeholder="Responsável, acompanhante ou igreja..." value="{{ request('search') }}" onchange="this.form.submit()">
                    </div>
                    @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Administração Local</label>
                            <select name="admlc_id" class="form-select no-choices" onchange="this.form.submit()">
                                <option value="">-- Todas --</option>
                                @foreach($locais as $localOpt)
                                    <option value="{{ $localOpt->admlc_id }}" {{ request('admlc_id') == $localOpt->admlc_id ? 'selected' : '' }}>
                                        {{ $localOpt->adm_local }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Situação/Status</label>
                        <select name="status" class="form-select no-choices" onchange="this.form.submit()">
                            <option value="">-- Todos --</option>
                            <option value="Pendente" {{ request('status') === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="Confirmado" {{ request('status') === 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="Reagendado" {{ request('status') === 'Reagendado' ? 'selected' : '' }}>Reagendado</option>
                            <option value="Cancelado" {{ request('status') === 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-2 d-flex align-items-end gap-1">
                        <button class="btn btn-outline-secondary w-100" type="submit">
                            <i class="ti ti-search"></i> Filtrar
                        </button>
                        @if(request('search') || request('admlc_id') || request('status'))
                            <a href="{{ route('agendamentos.index') }}" class="btn btn-outline-danger">Limpar</a>
                        @endif
                    </div>
                </form>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs mb-4" id="agendamentosTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-view" type="button" role="tab" aria-controls="calendar-view" aria-selected="true">
                            <i class="ti ti-calendar me-1"></i> Calendário Mensal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="list-tab" data-bs-toggle="tab" data-bs-target="#list-view" type="button" role="tab" aria-controls="list-view" aria-selected="false">
                            <i class="ti ti-list me-1"></i> Lista de Registros
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="agendamentosTabsContent">
                    <!-- Tab 1: Calendar View -->
                    <div class="tab-pane fade show active" id="calendar-view" role="tabpanel" aria-labelledby="calendar-tab">
                        <div class="p-2 border rounded bg-light">
                            <div id="calendar" style="min-height: 600px;"></div>
                        </div>
                    </div>

                    <!-- Tab 2: List View -->
                    <div class="tab-pane fade" id="list-view" role="tabpanel" aria-labelledby="list-tab">
                        @if($agendamentos->isEmpty())
                            <div class="text-center p-5">
                                <i class="ti ti-calendar-event text-muted" style="font-size: 48px;"></i>
                                <h5 class="mt-3">Nenhum agendamento cadastrado ou encontrado</h5>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Igreja / Localidade</th>
                                            <th>Data / Horário</th>
                                            <th>Inventariante</th>
                                            <th>Responsável Local / Contato</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="min-width: 150px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($agendamentos as $a)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $a->igreja ? $a->igreja->igreja : 'Não identificada' }}</span>
                                                    <small class="d-block text-muted">
                                                        <i class="ti ti-building-community"></i> {{ $a->local->adm_local ?? 'N/A' }}
                                                     </small>
                                                </td>
                                                <td>
                                                    <span class="fw-bold"><i class="ti ti-calendar"></i> {{ date('d/m/Y', strtotime($a->data)) }}</span>
                                                    <small class="d-block text-muted">
                                                        <i class="ti ti-clock"></i> {{ substr($a->horario, 0, 5) }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="text-dark">{{ $a->responsavel_nome }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark">{{ $a->acompanhante_nome ?? '-' }}</span>
                                                    @if($a->responsavel_telefone)
                                                        <small class="d-block text-muted">
                                                            <i class="ti ti-phone"></i> {{ $a->responsavel_telefone }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($a->status === 'Confirmado')
                                                        <span class="badge bg-success">Confirmado</span>
                                                    @elseif($a->status === 'Reagendado')
                                                        <span class="badge bg-primary">Reagendado</span>
                                                    @elseif($a->status === 'Pendente')
                                                        <span class="badge bg-warning text-dark">Pendente</span>
                                                    @elseif($a->status === 'Cancelado')
                                                        <span class="badge bg-danger">Cancelado</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $a->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-icon btn-light-info btn-ver-detalhes" 
                                                            data-id="{{ $a->id }}"
                                                            data-igreja="{{ $a->igreja ? $a->igreja->igreja : '' }}"
                                                            data-igreja-id="{{ $a->igreja_id }}"
                                                            data-admlc-id="{{ $a->admlc_id }}"
                                                            data-local="{{ $a->local->adm_local ?? 'N/A' }}"
                                                            data-data="{{ date('d/m/Y', strtotime($a->data)) }}"
                                                            data-data-raw="{{ $a->data }}"
                                                            data-horario="{{ substr($a->horario, 0, 5) }}"
                                                            data-responsavel="{{ $a->responsavel_nome }}"
                                                            data-telefone="{{ $a->responsavel_telefone }}"
                                                            data-acompanhante="{{ $a->acompanhante_nome }}"
                                                            data-status="{{ $a->status }}"
                                                            data-observacao="{{ $a->observacao }}"
                                                            data-motivo="{{ $a->motivo_cancelamento }}"
                                                            data-operador="{{ $a->operator ? $a->operator->name : 'N/A' }}"
                                                            title="Ver Detalhes/Ações">
                                                            <i class="ti ti-eye"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer d-flex justify-content-end border-top-0 bg-transparent">
                                {{ $agendamentos->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Novo Agendamento -->
@if(!Auth::user()->isAuditor())
<div class="modal fade" id="modalNovoAgendamento" tabindex="-1" aria-labelledby="modalNovoAgendamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white" id="modalNovoAgendamentoLabel"><i class="ti ti-calendar-plus me-2"></i>Agendar Novo Inventário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('agendamentos.store') }}" method="POST" id="form-novo-agendamento">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                            <div class="col-md-12">
                                <label for="modal_admlc_id" class="form-label fw-bold">Administração Local</label>
                                <select id="modal_admlc_id" class="form-select no-choices" required>
                                    <option value="" disabled selected>-- Selecione a Administração Local --</option>
                                    @foreach($locais as $localOpt)
                                        <option value="{{ $localOpt->admlc_id }}">
                                            {{ $localOpt->adm_local }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="igreja_id" class="form-label fw-bold">Casa de Oração / Localidade (Igreja)</label>
                                <select name="igreja_id" id="igreja_id" class="form-select no-choices" required disabled>
                                    <option value="" disabled selected>-- Selecione primeiro a Administração --</option>
                                </select>
                            </div>
                        @else
                            <div class="col-md-12">
                                <label for="igreja_id" class="form-label fw-bold">Casa de Oração / Localidade (Igreja)</label>
                                <select name="igreja_id" id="igreja_id" class="form-select no-choices" required>
                                    <option value="" disabled selected>-- Selecione a Localidade --</option>
                                    @foreach($igrejas as $ig)
                                        <option value="{{ $ig->id }}">
                                            {{ $ig->igreja }} (SIGA: {{ $ig->cod_siga }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label for="responsavel_nome" class="form-label fw-bold">Inventariante (Irmão que fará o inventário)</label>
                            @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                                <select name="responsavel_nome" id="responsavel_nome" class="form-select no-choices" required disabled>
                                    <option value="" disabled selected>-- Selecione primeiro a Administração --</option>
                                </select>
                            @else
                                <select name="responsavel_nome" id="responsavel_nome" class="form-select no-choices" required>
                                    <option value="" disabled {{ !collect($usuarios)->contains('name', Auth::user()->name) ? 'selected' : '' }}>-- Selecione o Inventariante --</option>
                                    @foreach($usuarios as $usr)
                                        <option value="{{ $usr->name }}" {{ Auth::user()->name === $usr->name ? 'selected' : '' }}>
                                            {{ $usr->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="acompanhante_nome" class="form-label fw-bold">Responsável Local (Irmão que acompanhará)</label>
                            <input type="text" name="acompanhante_nome" id="acompanhante_nome" class="form-control" placeholder="Membro do conselho ou auxiliar">
                        </div>
                        <div class="col-md-6">
                            <label for="responsavel_telefone" class="form-label fw-bold">Telefone do Responsável Local</label>
                            <input type="text" name="responsavel_telefone" id="responsavel_telefone" class="form-control" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-3">
                            <label for="data" class="form-label fw-bold">Data Proposta/Agendada</label>
                            <input type="date" name="data" id="data_modal" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label for="horario" class="form-label fw-bold">Horário</label>
                            <input type="time" name="horario" id="horario_modal" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label for="status" class="form-label fw-bold">Status Inicial</label>
                            <select name="status" id="status" class="form-select no-choices" required>
                                <option value="Confirmado" selected>Confirmado (Visita Agendada)</option>
                                <option value="Pendente">Pendente (Aguardando Retorno/Contato)</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="observacao" class="form-label fw-bold">Observações Iniciais</label>
                            <textarea name="observacao" id="observacao" class="form-control" rows="3" placeholder="Insira detalhes adicionais sobre o contato, pontos de atenção para o inventário, etc."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Registrar Agendamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal: Detalhes e Ações do Agendamento -->
<div class="modal fade" id="modalDetalhesAgendamento" tabindex="-1" aria-labelledby="modalDetalhesAgendamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white" id="modalDetalhesAgendamentoLabel"><i class="ti ti-info-circle me-2"></i>Detalhes do Agendamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- General details card -->
                    <div class="col-md-12">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="mb-0 text-primary fw-bold" id="det-igreja">Nome da Comum</h4>
                                <span class="badge" id="det-status">Status</span>
                            </div>
                            <hr class="my-2">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <strong><i class="ti ti-calendar text-muted"></i> Data:</strong> <span id="det-data">00/00/0000</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="ti ti-clock text-muted"></i> Horário:</strong> <span id="det-horario">00:00</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="ti ti-user text-muted"></i> Inventariante:</strong> <span id="det-responsavel">Nome</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="ti ti-users text-muted"></i> Responsável Local:</strong> <span id="det-acompanhante">Não informado</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="ti ti-phone text-muted"></i> Telefone Responsável Local:</strong> <span id="det-telefone">Não informado</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong><i class="ti ti-user-edit text-muted"></i> Registrado Por:</strong> <span id="det-operador">Nome</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observação section -->
                    <div class="col-md-12" id="section-observacao">
                        <div class="border rounded p-3">
                            <h6 class="fw-bold mb-2"><i class="ti ti-notes text-muted me-1"></i>Observações e Histórico</h6>
                            <div class="bg-white p-2 border rounded" id="det-observacao" style="white-space: pre-line; max-height: 200px; overflow-y: auto;">
                                Observação...
                            </div>
                        </div>
                    </div>

                    <!-- Motivo cancelamento section -->
                    <div class="col-md-12 d-none" id="section-cancelamento">
                        <div class="border border-danger rounded p-3 bg-light-danger text-danger">
                            <h6 class="fw-bold mb-2"><i class="ti ti-alert-triangle me-1"></i>Motivo do Cancelamento</h6>
                            <div class="bg-white p-2 border border-danger-subtle rounded text-dark" id="det-motivo">
                                Motivo...
                            </div>
                        </div>
                    </div>

                    @if(!Auth::user()->isAuditor())
                        <!-- Action Forms Container (Dynamic Toggles) -->
                        <div class="col-md-12 d-none" id="container-reagendar">
                            <form action="" method="POST" id="form-reagendar" class="border border-primary rounded p-3 bg-light-primary">
                                @csrf
                                @method('PUT')
                                <h6 class="fw-bold text-primary mb-3"><i class="ti ti-calendar-share me-1"></i>Reagendar Inventário</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="reagendar_data" class="form-label fw-bold small">Nova Data</label>
                                        <input type="date" name="data" id="reagendar_data" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="reagendar_horario" class="form-label fw-bold small">Novo Horário</label>
                                        <input type="time" name="horario" id="reagendar_horario" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="reagendar_observacao" class="form-label fw-bold small">Motivo do Reagendamento</label>
                                        <input type="text" name="observacao" id="reagendar_observacao" class="form-control" placeholder="Breve justificativa para constar no histórico" required>
                                    </div>
                                    <div class="col-12 text-end mt-3">
                                        <button type="button" class="btn btn-sm btn-secondary btn-cancelar-acao">Cancelar</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Confirmar Reagendamento</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 d-none" id="container-cancelar">
                            <form action="" method="POST" id="form-cancelar" class="border border-danger rounded p-3 bg-light-danger">
                                @csrf
                                @method('PUT')
                                <h6 class="fw-bold text-danger mb-3"><i class="ti ti-circle-x me-1"></i>Cancelar Agendamento</h6>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="motivo_cancelamento_input" class="form-label fw-bold small text-danger">Motivo do Cancelamento</label>
                                        <textarea name="motivo_cancelamento" id="motivo_cancelamento_input" class="form-control" rows="2" placeholder="Descreva de forma detalhada o motivo de cancelamento da visita" required></textarea>
                                    </div>
                                    <div class="col-12 text-end mt-3">
                                        <button type="button" class="btn btn-sm btn-secondary btn-cancelar-acao">Cancelar</button>
                                        <button type="submit" class="btn btn-sm btn-danger">Confirmar Cancelamento</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-12 d-none" id="container-editar">
                            <form action="" method="POST" id="form-editar" class="border border-info rounded p-3 bg-light-info">
                                @csrf
                                @method('PUT')
                                <h6 class="fw-bold text-info mb-3"><i class="ti ti-edit me-1"></i>Editar Dados de Contato</h6>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="edit_responsavel" class="form-label fw-bold small">Inventariante</label>
                                        <select name="responsavel_nome" id="edit_responsavel" class="form-select no-choices" required>
                                            <option value="" disabled selected>Carregando inventariantes...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_acompanhante" class="form-label fw-bold small">Responsável Local</label>
                                        <input type="text" name="acompanhante_nome" id="edit_acompanhante" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_telefone" class="form-label fw-bold small">Telefone do Responsável Local</label>
                                        <input type="text" name="responsavel_telefone" id="edit_telefone" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="edit_observacao" class="form-label fw-bold small">Observações</label>
                                        <textarea name="observacao" id="edit_observacao" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-12 text-end mt-3">
                                        <button type="button" class="btn btn-sm btn-secondary btn-cancelar-acao">Cancelar</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Salvar Alterações</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                <div>
                    @if(!Auth::user()->isAuditor())
                        <form action="" method="POST" id="form-excluir" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" id="btn-excluir-agendamento">
                                <i class="ti ti-trash"></i> Excluir
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="d-flex gap-1" id="buttons-normal-container">
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-success text-white dropdown-toggle" id="btn-whatsapp-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-brand-whatsapp"></i> Enviar WhatsApp
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btn-whatsapp-dropdown">
                            <li>
                                <button type="button" class="dropdown-item" id="btn-whatsapp-individual">
                                    <i class="ti ti-user me-2 text-success"></i> Individual (Resumida)
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item" id="btn-whatsapp-lista">
                                    <i class="ti ti-list me-2 text-success"></i> Lista de Próximos
                                </button>
                            </li>
                        </ul>
                    </div>
                    @if(!Auth::user()->isAuditor())
                        <button type="button" class="btn btn-info text-white" id="btn-toggle-editar">
                            <i class="ti ti-edit"></i> Editar Contatos
                        </button>
                        <button type="button" class="btn btn-primary" id="btn-toggle-reagendar">
                            <i class="ti ti-calendar-share"></i> Reagendar
                        </button>
                        <button type="button" class="btn btn-danger" id="btn-toggle-cancelar">
                            <i class="ti ti-circle-x"></i> Cancelar
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- FullCalendar styles and scripts -->
<style>
    .fc-header-toolbar {
        padding: 0.5rem;
        background: #fff;
        border-radius: 4px;
        margin-bottom: 1.25rem !important;
    }
    .fc-theme-bootstrap5 a {
        color: #033D60;
        text-decoration: none;
    }
    .fc-button-primary {
        background-color: #033D60 !important;
        border-color: #033D60 !important;
        text-transform: capitalize;
    }
    .fc-button-primary:hover {
        background-color: #022b44 !important;
        border-color: #022b44 !important;
    }
    .fc-button-active {
        background-color: #022b44 !important;
        border-color: #022b44 !important;
    }
    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.85em;
        border: none !important;
    }
    .bg-light-primary {
        background-color: rgba(3, 61, 96, 0.08) !important;
    }
    .bg-light-danger {
        background-color: rgba(231, 76, 60, 0.08) !important;
    }
    .bg-light-info {
        background-color: rgba(23, 162, 184, 0.08) !important;
    }
</style>

<!-- FullCalendar CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Choices.js Manual Initialization ---
        var choicesAdmlc = null;
        var choicesIgreja = null;
        var choicesResponsavel = null;
        var choicesEditResponsavel = null;

        var admlcEl = document.getElementById('modal_admlc_id');
        if (admlcEl) {
            choicesAdmlc = new Choices(admlcEl, {
                searchEnabled: true,
                itemSelectText: '',
                noResultsText: 'Nenhum resultado encontrado',
                noChoicesText: 'Sem opções disponíveis',
                placeholderValue: 'Selecione a Administração Local...',
                searchPlaceholderValue: 'Pesquisar...',
                allowHTML: true
            });
        }

        var igrejaEl = document.getElementById('igreja_id');
        if (igrejaEl) {
            choicesIgreja = new Choices(igrejaEl, {
                searchEnabled: true,
                itemSelectText: '',
                noResultsText: 'Nenhum resultado encontrado',
                noChoicesText: 'Sem opções disponíveis',
                placeholderValue: 'Selecione a Localidade...',
                searchPlaceholderValue: 'Pesquisar...',
                allowHTML: true
            });
        }

        var responsavelEl = document.getElementById('responsavel_nome');
        if (responsavelEl) {
            choicesResponsavel = new Choices(responsavelEl, {
                searchEnabled: true,
                itemSelectText: '',
                noResultsText: 'Nenhum resultado encontrado',
                noChoicesText: 'Sem opções disponíveis',
                placeholderValue: 'Selecione o Inventariante...',
                searchPlaceholderValue: 'Pesquisar...',
                allowHTML: true
            });
        }

        var editResponsavelEl = document.getElementById('edit_responsavel');
        if (editResponsavelEl) {
            choicesEditResponsavel = new Choices(editResponsavelEl, {
                searchEnabled: true,
                itemSelectText: '',
                noResultsText: 'Nenhum resultado encontrado',
                noChoicesText: 'Sem opções disponíveis',
                placeholderValue: 'Selecione o Inventariante...',
                searchPlaceholderValue: 'Pesquisar...',
                allowHTML: true
            });
        }

        // --- FullCalendar Initialization ---
        var calendarEl = document.getElementById('calendar');
        var isAuditor = @json(Auth::user()->isAuditor());
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            themeSystem: 'standard',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: {
                url: "{{ route('agendamentos.index') }}",
                extraParams: function() {
                    return {
                        search: "{{ request('search') }}",
                        admlc_id: "{{ request('admlc_id') }}",
                        status: "{{ request('status') }}"
                    };
                }
            },
            navLinks: true,
            editable: false,
            selectable: !isAuditor,
            selectMirror: true,
            dayMaxEvents: true,
            
            // Triggered when user clicks a day on the calendar to schedule a new inventory
            select: function(arg) {
                if (!isAuditor) {
                    var modal = new bootstrap.Modal(document.getElementById('modalNovoAgendamento'));
                    document.getElementById('data_modal').value = arg.startStr;
                    document.getElementById('horario_modal').value = "09:00"; // default proposed hour
                    modal.show();
                }
                calendar.unselect();
            },
            
            // Triggered when user clicks an event on the calendar
            eventClick: function(arg) {
                var props = arg.event.extendedProps;
                abrirModalDetalhes({
                    id: arg.event.id,
                    igreja: props.igreja_nome,
                    igreja_id: props.igreja_id,
                    admlc_id: props.admlc_id,
                    local_nome: props.local_nome,
                    data: props.data,
                    data_raw: props.data_raw,
                    horario: props.horario,
                    responsavel: props.responsavel_nome,
                    telefone: props.responsavel_telefone,
                    acompanhante: props.acompanhante_nome,
                    status: props.status,
                    observacao: props.observacao,
                    motivo: props.motivo_cancelamento,
                    operador: props.operador_nome
                });
            }
        });
        
        calendar.render();

        // Trigger calendar layout adjustment when tabs are switched
        var calendarTab = document.getElementById('calendar-tab');
        calendarTab.addEventListener('shown.bs.tab', function () {
            calendar.updateSize();
        });

        // --- Bind details view to Table rows ---
        document.querySelectorAll('.btn-ver-detalhes').forEach(button => {
            button.addEventListener('click', function() {
                var ds = this.dataset;
                abrirModalDetalhes({
                    id: ds.id,
                    igreja: ds.igreja,
                    igreja_id: ds.igrejaId,
                    admlc_id: ds.admlcId,
                    local_nome: ds.local,
                    data: ds.data,
                    data_raw: ds.dataRaw,
                    horario: ds.horario,
                    responsavel: ds.responsavel,
                    telefone: ds.telefone,
                    acompanhante: ds.acompanhante,
                    status: ds.status,
                    observacao: ds.observacao,
                    motivo: ds.motivo,
                    operador: ds.operador
                });
            });
        });

        // --- Open Details Modal Logic ---
        function abrirModalDetalhes(data) {
            // Fill normal text spans
            document.getElementById('det-igreja').innerText = data.igreja;
            document.getElementById('det-data').innerText = data.data;
            document.getElementById('det-horario').innerText = data.horario;
            document.getElementById('det-responsavel').innerText = data.responsavel;
            document.getElementById('det-telefone').innerText = data.telefone || 'Não informado';
            document.getElementById('det-acompanhante').innerText = data.acompanhante || 'Não informado';
            document.getElementById('det-operador').innerText = data.operador;
            document.getElementById('det-observacao').innerText = data.observacao || 'Nenhuma observação inicial.';

            // Status Badge
            var badge = document.getElementById('det-status');
            badge.className = 'badge';
            badge.innerText = data.status;
            if (data.status === 'Confirmado') badge.classList.add('bg-success');
            else if (data.status === 'Reagendado') badge.classList.add('bg-primary');
            else if (data.status === 'Pendente') badge.classList.add('bg-warning', 'text-dark');
            else if (data.status === 'Cancelado') badge.classList.add('bg-danger');

            // Handle Cancel Reason section
            var sectionCancel = document.getElementById('section-cancelamento');
            if (data.status === 'Cancelado' && data.motivo) {
                document.getElementById('det-motivo').innerText = data.motivo;
                sectionCancel.classList.remove('d-none');
            } else {
                sectionCancel.classList.add('d-none');
            }

            // Set Form Actions targets
            if (!isAuditor) {
                document.getElementById('form-reagendar').action = `/agendamentos/${data.id}/reagendar`;
                document.getElementById('form-cancelar').action = `/agendamentos/${data.id}/cancelar`;
                document.getElementById('form-editar').action = `/agendamentos/${data.id}`;
                document.getElementById('form-excluir').action = `/agendamentos/${data.id}`;
                
                // Prefill form inputs for Edit
                document.getElementById('edit_telefone').value = data.telefone || '';
                document.getElementById('edit_acompanhante').value = data.acompanhante || '';
                document.getElementById('edit_observacao').value = data.observacao || '';

                // Load active users dynamically for this administration local to populate choicesEditResponsavel
                if (choicesEditResponsavel) {
                    choicesEditResponsavel.disable();
                    choicesEditResponsavel.setChoices([{ value: '', label: 'Carregando inventariantes...', disabled: true, selected: true }], 'value', 'label', true);
                    
                    fetch(`/agendamentos/dados-por-local/${data.admlc_id}`)
                        .then(response => response.json())
                        .then(resData => {
                            var userChoices = [];
                            var foundSelected = false;
                            
                            resData.usuarios.forEach(u => {
                                var isSelected = (u.name === data.responsavel);
                                if (isSelected) foundSelected = true;
                                userChoices.push({
                                    value: u.name,
                                    label: u.name,
                                    selected: isSelected
                                });
                            });
                            
                            // If the current saved responsavel is not in the active users list, keep it as selected option anyway
                            if (!foundSelected && data.responsavel) {
                                userChoices.unshift({
                                    value: data.responsavel,
                                    label: `${data.responsavel} (Inativo/Não cadastrado)`,
                                    selected: true
                                });
                            }
                            
                            choicesEditResponsavel.clearStore();
                            choicesEditResponsavel.setChoices(userChoices, 'value', 'label', true);
                            choicesEditResponsavel.enable();
                        })
                        .catch(err => {
                            console.error('Error fetching users for edit select:', err);
                            choicesEditResponsavel.clearStore();
                            choicesEditResponsavel.setChoices([{ value: data.responsavel, label: data.responsavel, selected: true }], 'value', 'label', true);
                            choicesEditResponsavel.enable();
                        });
                }

                // Prefill form inputs for Reagendar
                document.getElementById('reagendar_data').value = data.data_raw;
                document.getElementById('reagendar_horario').value = data.horario;
            }

            // Close active containers and show main button layout
            fecharAcoesContainers();

            // Set Whatsapp button logic - Individual (Resumida)
            document.getElementById('btn-whatsapp-individual').onclick = function() {
                var tipoHeader = 'AGENDAMENTO';
                if (data.status === 'Reagendado') {
                    tipoHeader = 'REAGENDAMENTO';
                } else if (data.status === 'Cancelado') {
                    tipoHeader = 'CANCELAMENTO';
                }

                var msg = `*🙏 A Paz de Deus!*\n\n` +
                            `*📋✨ ${tipoHeader} DE INVENTÁRIO - CCB*\n\n` +
                            `🏛️ *Comum:* ${data.igreja}\n` +
                            `📅 *Data/Hora:* ${data.data} às ${data.horario}h\n` +
                            `👨‍💼 *Inventariante:* ${data.responsavel}\n` +
                            `🤝 *Resp. Local:* ${data.acompanhante || 'Não informado'}\n` +
                            `📞 *Telefone:* ${data.telefone || 'Não informado'}\n` +
                            `📌 *Status:* ${data.status}\n\n`;
                          
                if (data.status === 'Cancelado' && data.motivo) {
                    msg += `❌ *Motivo:* ${data.motivo}\n\n`;
                }
                
                if (data.observacao) {
                    msg += `📝 *Obs:* ${data.observacao}\n\n`;
                }
                
                msg += `Deus abençoe!`;

                navigator.clipboard.writeText(msg).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Texto copiado para a Área de Transferência!',
                        showConfirmButton: false,
                        timer: 2500
                    });

                    // Open whatsapp share URL
                    var encoded = encodeURIComponent(msg);
                    window.open(`https://api.whatsapp.com/send?text=${encoded}`, '_blank');
                });
            };

            // Set Whatsapp button logic - Lista de Próximos
            document.getElementById('btn-whatsapp-lista').onclick = function() {
                fetch(`/agendamentos/proximos-confirmados/${data.admlc_id}`)
                    .then(response => response.json())
                    .then(resData => {
                        if (!resData.proximos || resData.proximos.length === 0) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Nenhum agendamento futuro confirmado.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        var localName = data.local_nome || 'CCB';
                        var msg = `*🙏 A Paz de Deus!*\n\n` +
                                   `*📋✨ CRONOGRAMA DE INVENTÁRIOS*\n` +
                                   `🏛️ *Administração:* ${localName}\n\n` +
                                   `📅 *Próximas Visitas Confirmadas:*\n\n`;

                        resData.proximos.forEach(p => {
                            var dateParts = p.data.split('-');
                            var formattedDate = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
                            var formattedTime = p.horario.substring(0, 5);
                            var igrejaName = p.igreja ? p.igreja.igreja : 'Não identificada';

                            msg += `• *${formattedDate}* às *${formattedTime}h* - *${igrejaName}*\n` +
                                   `  👨‍💼 Inventariante: ${p.responsavel_nome}\n` +
                                   `  🤝 Resp. Local: ${p.acompanhante_nome || 'Não informado'}\n\n`;
                        });

                        msg += `Deus abençoe grandemente!`;

                        navigator.clipboard.writeText(msg).then(() => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Lista copiada para a Área de Transferência!',
                                showConfirmButton: false,
                                timer: 2500
                            });

                            var encoded = encodeURIComponent(msg);
                            window.open(`https://api.whatsapp.com/send?text=${encoded}`, '_blank');
                        });
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao carregar a lista de inventários.',
                            text: 'Por favor, tente novamente.',
                            confirmButtonText: 'OK'
                        });
                    });
            };

            // Show the modal
            var modal = new bootstrap.Modal(document.getElementById('modalDetalhesAgendamento'));
            modal.show();
        }

        // --- Action containers toggle logic ---
        if (!isAuditor) {
            var btnEditar = document.getElementById('btn-toggle-editar');
            var btnReagendar = document.getElementById('btn-toggle-reagendar');
            var btnCancelar = document.getElementById('btn-toggle-cancelar');

            var contEditar = document.getElementById('container-editar');
            var contReagendar = document.getElementById('container-reagendar');
            var contCancelar = document.getElementById('container-cancelar');

            var normalButtons = document.getElementById('buttons-normal-container');

            function fecharAcoesContainers() {
                contEditar.classList.add('d-none');
                contReagendar.classList.add('d-none');
                contCancelar.classList.add('d-none');
                
                // show togglers
                btnEditar.classList.remove('d-none');
                btnReagendar.classList.remove('d-none');
                btnCancelar.classList.remove('d-none');
            }

            btnEditar.addEventListener('click', function() {
                fecharAcoesContainers();
                contEditar.classList.remove('d-none');
                this.classList.add('d-none');
            });

            btnReagendar.addEventListener('click', function() {
                fecharAcoesContainers();
                contReagendar.classList.remove('d-none');
                this.classList.add('d-none');
            });

            btnCancelar.addEventListener('click', function() {
                fecharAcoesContainers();
                contCancelar.classList.remove('d-none');
                this.classList.add('d-none');
            });

            document.querySelectorAll('.btn-cancelar-acao').forEach(btn => {
                btn.addEventListener('click', function() {
                    fecharAcoesContainers();
                });
            });

            // SweetAlert for Delete schedule
            var btnExcluir = document.getElementById('btn-excluir-agendamento');
            var formExcluir = document.getElementById('form-excluir');
            if (btnExcluir && formExcluir) {
                formExcluir.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Excluir Agendamento?',
                        text: "Esta ação apagará permanentemente o agendamento da visita no cronograma!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74c3c',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Voltar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formExcluir.submit();
                        }
                    });
                });
            }
        }

        // Dynamic loading of churches and users in Novo Agendamento modal for system/regional admins
        var modalAdmlc = document.getElementById('modal_admlc_id');
        if (modalAdmlc) {
            modalAdmlc.addEventListener('change', function() {
                var admlcId = this.value;
                if (!admlcId) return;

                if (choicesIgreja) {
                    choicesIgreja.disable();
                    choicesIgreja.setChoices([{ value: '', label: 'Carregando localidades...', disabled: true, selected: true }], 'value', 'label', true);
                }
                if (choicesResponsavel) {
                    choicesResponsavel.disable();
                    choicesResponsavel.setChoices([{ value: '', label: 'Carregando inventariantes...', disabled: true, selected: true }], 'value', 'label', true);
                }
                
                fetch(`/agendamentos/dados-por-local/${admlcId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Populate Churches
                        if (choicesIgreja) {
                            var igrejaChoices = [{ value: '', label: '-- Selecione a Localidade --', disabled: true, selected: true }];
                            data.igrejas.forEach(ig => {
                                igrejaChoices.push({
                                    value: ig.id,
                                    label: `${ig.igreja} (SIGA: ${ig.cod_siga})`
                                });
                            });
                            choicesIgreja.clearStore();
                            choicesIgreja.setChoices(igrejaChoices, 'value', 'label', true);
                            choicesIgreja.enable();
                        }

                        // Populate Active Users (Inventariantes)
                        if (choicesResponsavel) {
                            var currentUserName = @json(Auth::user()->name);
                            var hasPreselected = false;
                            var userChoices = [];
                            
                            data.usuarios.forEach(u => {
                                var isSelected = (u.name === currentUserName);
                                if (isSelected) hasPreselected = true;
                                userChoices.push({
                                    value: u.name,
                                    label: u.name,
                                    selected: isSelected
                                });
                            });
                            
                            userChoices.unshift({
                                value: '',
                                label: '-- Selecione o Inventariante --',
                                disabled: true,
                                selected: !hasPreselected
                            });

                            choicesResponsavel.clearStore();
                            choicesResponsavel.setChoices(userChoices, 'value', 'label', true);
                            choicesResponsavel.enable();
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching dynamic data:', err);
                        if (choicesIgreja) {
                            choicesIgreja.clearStore();
                            choicesIgreja.setChoices([{ value: '', label: 'Erro ao carregar. Tente novamente.', disabled: true, selected: true }], 'value', 'label', true);
                        }
                        if (choicesResponsavel) {
                            choicesResponsavel.clearStore();
                            choicesResponsavel.setChoices([{ value: '', label: 'Erro ao carregar. Tente novamente.', disabled: true, selected: true }], 'value', 'label', true);
                        }
                    });
            });
        }
    });
</script>
@endsection
