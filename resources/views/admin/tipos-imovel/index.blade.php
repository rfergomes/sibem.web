@extends('layouts.app')

@section('title', 'Tipos de Imóvel')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-folders me-2"></i>Tipos de Imóvel</h4>
                @if(Auth::user()->isAdminSistema())
                    <a href="{{ route('admin.tipos-imovel.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Novo Tipo
                    </a>
                @endif
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.tipos-imovel.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por nome..." value="{{ request('search') }}" onchange="this.form.submit()">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.tipos-imovel.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($tiposImovel->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-folders text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhum tipo de imóvel cadastrado ou encontrado</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%;">ID</th>
                                    <th>Nome / Descrição do Tipo</th>
                                    <th>Igrejas</th>
                                    <th>Cadastrado em</th>
                                    <th class="text-end" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tiposImovel as $tipo)
                                    <tr>
                                        <td>
                                            <span class="fw-bold badge bg-light-primary text-primary px-2">{{ $tipo->id }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $tipo->nome }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-info text-info fw-bold">{{ $tipo->igrejas_count }}</span>
                                        </td>
                                        <td><small>{{ $tipo->created_at ? $tipo->created_at->format('d/m/Y H:i') : 'N/A' }}</small></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('admin.tipos-imovel.show', $tipo->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes e Igrejas">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if(Auth::user()->isAdminSistema())
                                                    <a href="{{ route('admin.tipos-imovel.edit', $tipo->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Tipo">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.tipos-imovel.destroy', $tipo->id) }}" method="POST" class="d-inline-block delete-type-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Tipo">
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
                        {{ $tiposImovel->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-type-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Tipo de Imóvel?',
                text: "Esta ação apagará permanentemente o tipo do sistema e só é permitida se nenhuma igreja estiver associada!",
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
