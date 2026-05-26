@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="row">
    <!-- User Profile Details Card -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-user me-2"></i>Perfil do Usuário</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 72px; height: 72px; font-size: 28px; font-weight: 600;">
                        {{ substr($usuario->name, 0, 2) }}
                    </div>
                    <h4>{{ $usuario->name }}</h4>
                    <p class="text-muted">{{ $usuario->email }}</p>
                </div>

                <table class="table table-borderless align-middle">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">Perfil:</td>
                            <td>
                                @php
                                    $badgeClass = match($usuario->tipo) {
                                        'admin_sistema' => 'bg-light-danger text-danger',
                                        'admin_regional' => 'bg-light-primary text-primary',
                                        'admin_local' => 'bg-light-success text-success',
                                        'operador' => 'bg-light-info text-info',
                                        default => 'bg-light-secondary text-secondary'
                                    };
                                    $labelText = match($usuario->tipo) {
                                        'admin_sistema' => 'Super Admin',
                                        'admin_regional' => 'Admin Regional',
                                        'admin_local' => 'Admin Local',
                                        'operador' => 'Operador',
                                        'auditor' => 'Auditor',
                                        default => $usuario->tipo
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $labelText }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Telefone:</td>
                            <td>{{ $usuario->telefone ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Admin Local:</td>
                            <td>{{ $usuario->local->nome ?? 'Nenhum' }}</td>
                        </tr>
                        @if($usuario->local && $usuario->local->regional)
                            <tr>
                                <td class="fw-bold text-muted">Regional:</td>
                                <td>{{ $usuario->local->regional->adm_regional }} ({{ $usuario->local->regional->uf }})</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="fw-bold text-muted">Comum Congregação:</td>
                            <td>{{ $usuario->igreja ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Cidade:</td>
                            <td>{{ $usuario->cidade ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Cadastrado em:</td>
                            <td>{{ $usuario->created_at ? $usuario->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 text-center">
                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-edit"></i> Editar Perfil
                    </a>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light-secondary btn-sm ms-2">
                        Voltar para Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tokens Card -->
    <div class="col-md-7">
        <!-- Generate Token Form -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0 text-white"><i class="ti ti-key me-2"></i>Gerar Token Manual (Acesso Desktop)</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.usuarios.tokens.gerar', $usuario->id) }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Descrição da Máquina / Computador</label>
                            <input type="text" name="dispositivo" class="form-control" placeholder="Ex: Recepção, Notebook Rodrigo, PC Comum, etc." required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="ti ti-plus me-1"></i> Gerar Token
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Token Listing -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0 text-white"><i class="ti ti-list me-2"></i>Tokens de Acesso Ativos</h5>
            </div>
            <div class="card-body p-0">
                @if($tokens->isEmpty())
                    <div class="text-center p-5">
                        <p class="text-muted mb-0">Nenhum token de acesso desktop ativo para este usuário.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Máquina / Dispositivo</th>
                                    <th>Token</th>
                                    <th>Status</th>
                                    <th>Gerado em</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokens as $token)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $token->dispositivo }}</span>
                                        </td>
                                        <td>
                                            <code>{{ $token->token }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-success text-success">Ativo</span>
                                        </td>
                                        <td>
                                            <small>{{ $token->created_at ? $token->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.tokens.destroy', $token->id) }}" method="POST" class="d-inline-block revoke-token-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Revogar Token">
                                                    <i class="ti ti-power me-1"></i> Revogar
                                                </button>
                                            </form>
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
    document.querySelectorAll('.revoke-token-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Revogar Token?',
                text: "O aplicativo desktop nesta máquina perderá o acesso de escrita aos bancos de dados locais imediatamente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f15bb5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, revogar!',
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
