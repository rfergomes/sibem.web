@extends('layouts.app')

@section('title', 'Dependências Globais')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-folders me-2"></i>Dependências Globais</h4>
                @if(Auth::user()->isAdminSistema())
                    <a href="{{ route('admin.dependencias.create') }}" class="btn btn-outline-info btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Nova Dependência
                    </a>
                @endif
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.dependencias.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por descrição..." value="{{ request('search') }}" onchange="this.form.submit()">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.dependencias.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($dependencias->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-folders text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhuma dependência cadastrada ou encontrada</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Dependência</th>
                                    <th class="text-center" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dependencias as $dep)
                                    <tr>
                                        <td>
                                            <span class="fw-bold"></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $dep->dependencia_id }} - {{ $dep->descricao }}</span>
                                            <small class="d-block text-muted">
                                                {{ $dep->criado_por->name }} em {{ $dep->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.dependencias.show', $dep->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if(Auth::user()->isAdminSistema())
                                                    <a href="{{ route('admin.dependencias.edit', $dep->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Dependência">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.dependencias.destroy', $dep->id) }}" method="POST" class="d-inline-block delete-dependency-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Dependência">
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
                        {{ $dependencias->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-dependency-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Dependência?',
                text: "Esta ação apagará permanentemente a dependência global do sistema!",
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
