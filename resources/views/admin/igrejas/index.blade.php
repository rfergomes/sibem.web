@extends('layouts.app')

@section('title', 'Gerenciamento de Igrejas (CCB)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-building-community me-2"></i>Igrejas Cadastradas (CCB)</h4>
                @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                    <a href="{{ route('admin.igrejas.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Nova Igreja
                    </a>
                @endif
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.igrejas.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por comum, siga, cnpj, cidade..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.igrejas.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($igrejas->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-building-community text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhuma igreja cadastrada ou encontrada</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cód. SIGA</th>
                                    <th>Comum Congregação</th>
                                    <th>Tipo de Imóvel</th>
                                    <th>Administração Local</th>
                                    <th>Cidade / UF</th>
                                    <th class="text-end" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($igrejas as $igreja)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light-primary text-primary fw-bold" style="font-size: 0.9em;">{{ $igreja->cod_siga }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $igreja->igreja }}</span>
                                        </td>
                                        <td>
                                            @if($igreja->tipoImovel)
                                                <span class="badge bg-light-info text-info">{{ $igreja->tipoImovel->nome }}</span>
                                            @else
                                                <span class="text-muted">Não definido</span>
                                            @endif
                                        </td>
                                        <td>{{ $igreja->local->nome ?? 'N/A' }}</td>
                                        <td>{{ $igreja->cidade ?? 'N/A' }} / {{ $igreja->uf ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('admin.igrejas.show', $igreja->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                                                    <a href="{{ route('admin.igrejas.edit', $igreja->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Igreja">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.igrejas.destroy', $igreja->id) }}" method="POST" class="d-inline-block delete-igreja-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Igreja">
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
                        {{ $igrejas->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-igreja-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Igreja?',
                text: "Esta ação apagará permanentemente os dados cadastrados desta igreja! Certifique-se de que não há inventários ativos para ela.",
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
