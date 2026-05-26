@extends('layouts.app')

@section('title', 'Administrações Regionais')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-map-pin me-2"></i>Administrações Regionais</h4>
                <a href="{{ route('admin.regionais.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
                    <i class="ti ti-plus me-1"></i> Nova Regional
                </a>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.regionais.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por regional ou UF..." value="{{ request('search') }}">
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
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Regional</th>
                                    <th>Administração Regional</th>
                                    <th>UF</th>
                                    <th>Cadastrada em</th>
                                    <th class="text-end" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regionais as $reg)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $reg->admrg_id }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $reg->adm_regional }}</span>
                                        </td>
                                        <td><span class="badge bg-light-primary text-primary">{{ $reg->uf }}</span></td>
                                        <td><small>{{ $reg->created_at ? $reg->created_at->format('d/m/Y H:i') : 'N/A' }}</small></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
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
