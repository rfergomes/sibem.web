@extends('layouts.app')

@section('title', 'Gerenciamento de Usuários')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-users me-2"></i>Usuários do Sistema</h4>
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
                    <i class="ti ti-plus me-1"></i> Novo Usuário
                </a>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.usuarios.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, e-mail, comum ou cidade..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($usuarios->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-users text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhum usuário cadastrado ou encontrado</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th>Perfil</th>
                                    <th>Administração Local</th>
                                    <th>Comum / Cidade</th>
                                    <th class="text-end" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usuarios as $user)
                                    <tr>
                                        <td>      
                                            @php
                                                $badgeClass = match($user->tipo) {
                                                    'admin_sistema' => 'bg-light-danger text-danger',
                                                    'admin_regional' => 'bg-light-primary text-primary',
                                                    'admin_local' => 'bg-light-success text-success',
                                                    'operador' => 'bg-light-info text-info',
                                                    default => 'bg-light-secondary text-secondary'
                                                };
                                                $labelText = match($user->tipo) {
                                                    'admin_sistema' => 'Super Admin',
                                                    'admin_regional' => 'Admin Regional',
                                                    'admin_local' => 'Admin Local',
                                                    'operador' => 'Operador',
                                                    'auditor' => 'Auditor',
                                                    default => $user->tipo
                                                };
                                            @endphp
                                            <span class="d-block fw-bold text-dark">{{ $user->name }}</span>
                                            <small class="badge align-items-le {{ $badgeClass }}">{{ $labelText }}</small>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->telefone ?? 'Não informado' }}</td>
                                        <td>
                                            {{ $user->local->nome ?? 'Nenhum' }}
                                        </td>
                                        <td>
                                            {{ $user->igreja ?? 'N/A' }}
                                            @if($user->cidade)
                                                <small class="text-muted d-block">{{ $user->cidade }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('admin.usuarios.show', $user->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Visualizar Detalhes e Tokens">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.usuarios.edit', $user->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Usuário">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                @if($user->id !== Auth::user()->id)
                                                    <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" class="d-inline-block delete-user-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Usuário">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer d-flex justify-content-end border-top-0 bg-transparent">
                        {{ $usuarios->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Usuário?',
                text: "Esta ação apagará permanentemente o usuário e revogará todos os seus tokens de acesso desktop associados!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f15bb5',
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
</script>
@endsection
