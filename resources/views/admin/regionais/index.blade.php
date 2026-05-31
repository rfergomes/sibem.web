@extends('layouts.app')

@section('title', 'Administrações Regionais')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-map-pin me-2"></i>Administrações Regionais</h4>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm me-2" role="group" aria-label="Visualização">
                        <button type="button" class="btn btn-outline-info btn-view-table" title="Visualização em Tabela">
                            <i class="ti ti-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info btn-view-cards" title="Visualização em Cards">
                            <i class="ti ti-layout-grid"></i>
                        </button>
                    </div>
                    <a href="{{ route('admin.regionais.create') }}" class="btn btn-outline-info btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Nova Regional
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.regionais.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por regional ou UF..." value="{{ request('search') }}" onchange="this.form.submit()">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.regionais.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($regionais->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-map text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhuma regional cadastrada ou encontrada</h5>
                    </div>
                @else
                    <!-- Visualização Desktop -->
                    <div class="table-responsive view-table">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Administração Regional</th>
                                    <th class="text-center">Localidades</th>
                                    <th class="text-center" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regionais as $reg)
                                    <tr>
                                        <td>                                           
                                            <span class="fw-bold text-primary">{{ $reg->admrg_id }} - </span>
                                            <span class="fw-bold text-dark">{{ $reg->adm_regional }} - </span>
                                            <span class="badge bg-light-primary text-primary">{{ $reg->uf }}</span>
                                            <small class="d-block text-muted">Criada em: {{ $reg->created_at ? $reg->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge fw-bold bg-light-primary text-primary">{{ $reg->locais_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.regionais.show', $reg->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Administrações Locais">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.regionais.edit', $reg->id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Regional">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.regionais.destroy', $reg->id) }}" method="POST" class="d-inline-block delete-regional-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Regional">
                                                        <i class="ti ti-trash"></i>
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
                    <div class="view-cards py-2">
                        @foreach($regionais as $reg)
                            <div class="card mb-3 border border-light-subtle shadow-sm rounded">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title fw-bold mb-0 text-primary">{{ $reg->adm_regional }}</h5>
                                        <span class="badge bg-light-primary text-primary">{{ $reg->uf }}</span>
                                    </div>
                                    
                                    <div class="mb-3 small text-dark">
                                        <div class="mb-1"><strong>Código Regional:</strong> <code>{{ $reg->admrg_id }}</code></div>
                                        <div class="mb-1"><strong>Localidades Vinculadas:</strong> <span class="badge bg-light-primary text-primary fw-bold">{{ $reg->locais_count }}</span></div>
                                        <small class="d-block text-muted mt-1"><i class="ti ti-calendar me-1"></i>Criada em: {{ $reg->created_at ? $reg->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </div>

                                    <div class="border-top pt-2 mt-2 d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.regionais.show', $reg->id) }}" class="btn btn-sm btn-light-info" title="Ver Administrações Locais">
                                            <i class="ti ti-eye me-1"></i> Localidades
                                        </a>
                                        <a href="{{ route('admin.regionais.edit', $reg->id) }}" class="btn btn-sm btn-light-primary" title="Editar Regional">
                                            <i class="ti ti-edit me-1"></i> Editar
                                        </a>
                                        <form action="{{ route('admin.regionais.destroy', $reg->id) }}" method="POST" class="d-inline-block delete-regional-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light-danger" title="Excluir Regional">
                                                <i class="ti ti-trash me-1"></i> Excluir
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card-footer d-flex justify-content-end border-top-0 bg-transparent">
                        {{ $regionais->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-regional-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Regional?',
                text: "Esta ação apagará permanentemente a regional. Ela não deve possuir Administrações Locais vinculadas!",
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
