@extends('layouts.app')

@section('title', 'Sistemas de Setores')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-chart-bar me-2"></i>Setores Cadastrados</h4>
                @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                    <a href="{{ route('admin.setores.create') }}" class="btn btn-outline-info btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Novo Setor
                    </a>
                @endif
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.setores.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por código ou descrição..." value="{{ request('search') }}" onchange="this.form.submit()">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.setores.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($setores->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-chart-bar text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhum setor cadastrado ou encontrado</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Setor</th>
                                    <th>Administrações</th>
                                    <th>Localidades</th>
                                    <th class="text-end" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($setores as $setor)
                                    <tr>
                                        <td><span class="fw-bold text-dark">{{ $setor->cod_setor }} - {{ $setor->descricao }}</span></td>
                                        <td>
                                            @foreach ($setor->localidades as $localidade)
                                                <span class="badge bg-light-primary text-primary fw-bold">{{ $localidade->nome }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $setor->local->regional->adm_regional ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-light-info text-info fw-bold">{{ $setor->igrejas_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.setores.show', $setor->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                                                    <a href="{{ route('admin.setores.edit', $setor->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Setor">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.setores.destroy', $setor->id) }}" method="POST" class="d-inline-block delete-setor-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Setor">
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
                        {{ $setores->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-setor-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Setor?',
                text: "Esta ação apagará permanentemente o setor cadastrado!",
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
