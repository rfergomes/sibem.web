@extends('layouts.app')

@section('title', 'Dashboard Gerencial')

@section('content')
<div class="row">
    <!-- Header Page Info -->
    <div class="col-12 mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h3>Dashboard Gerencial</h3>
            <p class="text-muted mb-0">Visão geral do sistema SIBEM. Administração Ativa: <span class="fw-bold text-primary">{{ $activeLocal->nome ?? 'Nenhuma' }}</span></p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card bg-grd-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Igrejas (CCB)</h6>
                        <h2 class="mb-0 text-white">{{ $stats['igrejas'] }}</h2>
                    </div>
                    <div class="avatar bg-white-20 rounded-3 text-white p-2">
                        <i class="ti ti-building" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card bg-grd-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Inventários Concluídos</h6>
                        <h2 class="mb-0 text-white">{{ $stats['inventarios_concluidos'] }}</h2>
                    </div>
                    <div class="avatar bg-white-20 rounded-3 text-white p-2">
                        <i class="ti ti-clipboard-check" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card bg-grd-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Inventários em Aberto</h6>
                        <h2 class="mb-0 text-white">{{ $stats['inventarios_abertos'] }}</h2>
                    </div>
                    <div class="avatar bg-white-20 rounded-3 text-white p-2">
                        <i class="ti ti-clipboard-list" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card bg-grd-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Usuários no Sistema</h6>
                        <h2 class="mb-0 text-white">{{ $stats['usuarios'] }}</h2>
                    </div>
                    <div class="avatar bg-white-20 rounded-3 text-white p-2">
                        <i class="ti ti-users" style="font-size: 32px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Extra Stats for Super Admin -->
    @if(Auth::user()->isAdminSistema())
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Estrutura Organizacional Central</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h3 class="text-primary">{{ $stats['regionais'] }}</h3>
                            <p class="text-muted mb-0">Regionais Cadastradas</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $stats['locais'] }}</h3>
                            <p class="text-muted mb-0">Locais (Tenants) Ativos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Token Requests -->
    <div class="col-md-{{ Auth::user()->isAdminSistema() ? '6' : '12' }} mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0"><i class="ti ti-key-off me-2"></i>Pedidos de Tokens Recentes (Desktop)</h5>
                <a href="{{ route('admin.token-requests.index') }}" class="btn btn-sm btn-link">Ver todos</a>
            </div>
            <div class="card-body p-0">
                @if($recentTokenRequests->isEmpty())
                    <div class="text-center p-4">
                        <p class="text-muted mb-0">Nenhuma solicitação de token pendente no momento.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <tbody>
                                @foreach($recentTokenRequests as $req)
                                    <tr>
                                        <td>
                                            <span class="fw-600 text-dark">{{ $req->user->name ?? 'Usuário Desconhecido' }}</span>
                                            <small class="d-block text-muted">{{ $req->user->email ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted d-block">Comum / Cidade</small>
                                            <span class="text-dark fw-500">{{ $req->user->igreja ?? 'N/A' }}</span>
                                            <small class="d-block text-muted">{{ $req->user->cidade ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted d-block">Status</small>
                                            <span class="badge bg-warning">Pendente</span>
                                        </td>
                                        <td class="text-end">
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(!$recentTokenRequests->isEmpty() && session('show_login_toast'))
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
        @endif
    });
</script>
@endsection
