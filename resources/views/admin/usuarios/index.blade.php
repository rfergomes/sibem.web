@extends('layouts.app')

@section('title', 'Gerenciamento de Usuários')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-users me-2"></i>Usuários do Sistema</h4>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm me-2" role="group" aria-label="Visualização">
                        <button type="button" class="btn btn-outline-light btn-view-table" title="Visualização em Tabela">
                            <i class="ti ti-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-light btn-view-cards" title="Visualização em Cards">
                            <i class="ti ti-layout-grid"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-outline-info btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Novo Usuário
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.usuarios.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nome, e-mail, comum ou cidade..." value="{{ request('search') }}" onchange="this.form.submit()">
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
                    <!-- Visualização Desktop -->
                    <div class="table-responsive view-table">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Contatos</th>
                                    <th>Administração</th>
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
                                            <small class="text-muted"><i class="ti ti-map-pin me-1"></i>{{ $user->igreja ?? 'N/A' }} | {{ $user->cidade ?? 'N/A' }}</small>
                                            
                                        </td>
                                        <td>
                                            <span class="d-block"><i class="ti ti-mail me-1"></i>{{ $user->email }}</span>
                                            <span class="d-block mt-1 text-muted"><i class="ti ti-brand-whatsapp me-1"></i>{{ $user->telefone ?? 'Não informado' }}</span>
                                        </td>
                                        <td>
                                            <span class="d-block fw-bold">{{ $user->local->nome ?? 'Nenhum' }}</span>
                                            <span class=" mt-1 badge align-items-center {{ $badgeClass }}">{{ $labelText }}</span>
                                        </td>
                                        <td class="align-items-center">
                                            
                                            <div class="d-flex justify-content-center gap-1">
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

                    <!-- Visualização Mobile -->
                    <div class="view-cards py-2">
                        @foreach($usuarios as $user)
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
                            <div class="card mb-3 border border-light-subtle shadow-sm rounded">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title fw-bold mb-0 text-dark">{{ $user->name }}</h5>
                                        <span class="badge {{ $badgeClass }}">{{ $labelText }}</span>
                                    </div>
                                    
                                    <div class="mb-3 small text-dark">
                                        @if($user->igreja || $user->cidade)
                                            <div class="mb-1"><i class="ti ti-map-pin text-muted me-1"></i>{{ $user->igreja ?? 'N/A' }} | {{ $user->cidade ?? 'N/A' }}</div>
                                        @endif
                                        <div class="mb-1"><i class="ti ti-mail text-muted me-1"></i>{{ $user->email }}</div>
                                        @if($user->telefone)
                                            <div class="mb-1"><i class="ti ti-brand-whatsapp text-muted me-1"></i>{{ $user->telefone }}</div>
                                        @endif
                                        <div class="mb-1"><i class="ti ti-building text-muted me-1"></i><strong>Local:</strong> {{ $user->local->nome ?? 'Nenhum' }}</div>
                                    </div>

                                    <div class="border-top pt-2 mt-2 d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.usuarios.show', $user->id) }}" class="btn btn-sm btn-light-info" title="Visualizar Detalhes e Tokens">
                                            <i class="ti ti-eye me-1"></i> Detalhes
                                        </a>
                                        <a href="{{ route('admin.usuarios.edit', $user->id) }}" class="btn btn-sm btn-light-primary" title="Editar Usuário">
                                            <i class="ti ti-edit me-1"></i> Editar
                                        </a>
                                        @if($user->id !== Auth::user()->id)
                                            <form action="{{ route('admin.usuarios.destroy', $user->id) }}" method="POST" class="d-inline-block delete-user-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger" title="Excluir Usuário">
                                                    <i class="ti ti-trash me-1"></i> Excluir
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
