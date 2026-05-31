@extends('layouts.app')

@section('title', 'Solicitações de Tokens Desktop')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-key me-2"></i>Solicitações de Tokens (Acesso Desktop)</h4>
                <div class="btn-group btn-group-sm" role="group" aria-label="Visualização">
                    <button type="button" class="btn btn-outline-light btn-view-table" title="Visualização em Tabela">
                        <i class="ti ti-list"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light btn-view-cards" title="Visualização em Cards">
                        <i class="ti ti-layout-grid"></i>
                    </button>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if($solicitacoes->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-key text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhuma solicitação encontrada</h5>
                        <p class="text-muted">Novas solicitações enviadas pelo aplicativo desktop aparecerão aqui.</p>
                    </div>
                @else
                    <!-- Visualização Desktop -->
                    <div class="table-responsive view-table">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Usuário</th>
                                    <th>E-mail / Telefone</th>
                                    <th>Comum / Cidade</th>
                                    <th>Identificador Máquina</th>
                                    <th>Status</th>
                                    <th class="text-end" style="min-width: 320px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitacoes as $solicitacao)
                                    <tr>
                                        <td>
                                            {{ $solicitacao->created_at->format('d/m/Y H:i') }}
                                            <small class="text-muted d-block">{{ $solicitacao->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $solicitacao->user->name ?? 'Usuário Desconhecido' }}</span>
                                        </td>
                                        <td>
                                            {{ $solicitacao->user->email ?? 'N/A' }}
                                            @if($solicitacao->user && $solicitacao->user->telefone)
                                                <small class="text-muted d-block">{{ $solicitacao->user->telefone }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $solicitacao->user->igreja ?? 'N/A' }}
                                            @if($solicitacao->user && $solicitacao->user->cidade)
                                                <small class="text-muted d-block">{{ $solicitacao->user->cidade }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $solicitacao->dispositivo }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-warning text-warning">Pendente</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-1">
                                                <form action="{{ route('admin.token-requests.approve', $solicitacao->id) }}" method="POST" class="d-inline-block approve-form">
                                                    @csrf
                                                    <div class="input-group input-group-sm">
                                                        <select name="admlc_id" class="form-select form-select-sm" style="max-width: 200px;" required>
                                                            <option value="">Associar Administração...</option>
                                                            @foreach($locais as $local)
                                                                <option value="{{ $local->admlc_id }}">{{ $local->nome }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-secondary d-flex align-items-center">
                                                            <i class="ti ti-check me-1"></i> Aprovar
                                                        </button>
                                                    </div>
                                                </form>
                                                <form action="{{ route('admin.token-requests.reject', $solicitacao->id) }}" method="POST" class="d-inline-block reject-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger d-flex align-items-center" title="Rejeitar Solicitação">
                                                        <i class="ti ti-x"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Visualização Mobile -->
                    <div class="view-cards p-3">
                        @foreach($solicitacoes as $solicitacao)
                            <div class="card mb-3 border border-light-subtle shadow-sm rounded">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted fw-bold"><i class="ti ti-calendar"></i> {{ $solicitacao->created_at->format('d/m/Y H:i') }}</small>
                                        <span class="badge bg-light-warning text-warning">Pendente</span>
                                    </div>
                                    <h5 class="card-title fw-bold mb-2 text-primary">{{ $solicitacao->user->name ?? 'Usuário Desconhecido' }}</h5>
                                    
                                    <div class="mb-3 small text-dark">
                                        <div class="mb-1"><i class="ti ti-mail text-muted me-1"></i><strong>E-mail:</strong> {{ $solicitacao->user->email ?? 'N/A' }}</div>
                                        @if($solicitacao->user && $solicitacao->user->telefone)
                                            <div class="mb-1"><i class="ti ti-phone text-muted me-1"></i><strong>Tel:</strong> {{ $solicitacao->user->telefone }}</div>
                                        @endif
                                        <div class="mb-1"><i class="ti ti-building-community text-muted me-1"></i><strong>Localidade:</strong> {{ $solicitacao->user->igreja ?? 'N/A' }}@if($solicitacao->user && $solicitacao->user->cidade) ({{ $solicitacao->user->cidade }})@endif</div>
                                        <div class="mb-1"><i class="ti ti-device-laptop text-muted me-1"></i><strong>Máquina:</strong> <code>{{ $solicitacao->dispositivo }}</code></div>
                                    </div>

                                    <div class="border-top pt-3 mt-2">
                                        <form action="{{ route('admin.token-requests.approve', $solicitacao->id) }}" method="POST" class="approve-form mb-2">
                                            @csrf
                                            <div class="d-flex flex-column gap-2">
                                                <select name="admlc_id" class="form-select form-select-sm" required>
                                                    <option value="">Associar Administração...</option>
                                                    @foreach($locais as $local)
                                                        <option value="{{ $local->admlc_id }}">{{ $local->nome }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-secondary w-100 d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-check me-1"></i> Aprovar Solicitação
                                                </button>
                                            </div>
                                        </form>
                                        <form action="{{ route('admin.token-requests.reject', $solicitacao->id) }}" method="POST" class="reject-form mt-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-x me-1"></i> Rejeitar Solicitação
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="card-footer d-flex justify-content-end border-top-0 bg-transparent">
                        {{ $solicitacoes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    /* Evita que a tabela oculte o dropdown do Choices.js no desktop */
    @media (min-width: 768px) {
        .table-responsive {
            overflow: visible !important;
        }
    }
    
    /* Z-index para sobrepor menus do Choices.js sobre outros elementos */
    .choices {
        z-index: 1050 !important;
    }
    .choices__list--dropdown {
        z-index: 1050 !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.approve-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const select = form.querySelector('select[name="admlc_id"]');
            if (!select.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Atenção!',
                    text: "Por favor, selecione uma Administração Local para associar ao token.",
                    icon: 'warning',
                    confirmButtonColor: '#f15bb5'
                });
                return;
            }

            e.preventDefault();
            Swal.fire({
                title: 'Aprovar Solicitação?',
                text: "Isso ativará o token de acesso para a máquina especificada na Administração Local selecionada.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ca58d',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, aprovar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('.reject-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Rejeitar Solicitação?',
                text: "O acesso de cadastro para essa máquina será indeferido e a solicitação será apagada.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f15bb5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, rejeitar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
