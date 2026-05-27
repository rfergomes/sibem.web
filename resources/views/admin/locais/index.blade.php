@extends('layouts.app')

@section('title', 'Administrações Locais')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-building me-2"></i>Administrações Locais</h4>
                @if(Auth::user()->isAdminSistema())
                    <a href="{{ route('admin.locais.create') }}" class="btn btn-primary btn-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i> Nova Localidade
                    </a>
                @endif
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.locais.index') }}" method="GET" class="row g-3 mb-4" id="filter-form">
                    <div class="col-md-5 col-lg-4">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por local, razão social, CNPJ ou cidade..." value="{{ request('search') }}">
                    </div>
                    @if(Auth::user()->isAdminSistema())
                        <div class="col-md-4 col-lg-3">
                            <select name="admrg_id" id="admrg_id_select" class="form-select">
                                <option value="">-- Todas as Regionais --</option>
                                @foreach($regionais as $regOpt)
                                    <option value="{{ $regOpt->admrg_id }}" {{ request('admrg_id') == $regOpt->admrg_id ? 'selected' : '' }}>
                                        {{ $regOpt->adm_regional }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="ti ti-search"></i> Filtrar
                        </button>
                        @if(request('search') || request('admrg_id'))
                            <a href="{{ route('admin.locais.index') }}" class="btn btn-outline-danger">Limpar</a>
                        @endif
                    </div>
                </form>

                @if($locais->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-building text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhuma Administração Local cadastrada ou encontrada</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>

                                    <th>Administração Local</th>

                                    <th>Cidade / UF</th>
                                    <th>Regional</th>
                                    <th>Igrejas / Setores</th>
                                    <th class="text-end" style="min-width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($locais as $local)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $local->adm_local }}</span>
                                            <span class="d-block"><span class="badge bg-light-primary text-primary">{{ $local->admlc_id }}</span> / <code>{{ $local->cnpj }}</code></span>
                                        </td>
                                        <td>{{ $local->cidade }} / <span class="badge bg-light-primary text-primary">{{ $local->uf }}</span></td>
                                        <td>{{ $local->regional->adm_regional ?? 'N/A' }}
                                            <span class="d-block">{{ $local->cidade }} / <span class="badge bg-light-primary text-primary">{{ $local->uf }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-info text-info fw-bold me-1" title="Igrejas">
                                                {{ $local->igrejas_count }} <i class="ti ti-building-community"></i>
                                            </span>
                                            <span class="badge bg-light-secondary text-secondary fw-bold" title="Setores">
                                                {{ $local->setores_count }} <i class="ti ti-chart-bar"></i>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <a href="{{ route('admin.locais.show', $local->admlc_id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if(Auth::user()->isAdminSistema())
                                                    <a href="{{ route('admin.locais.edit', $local->admlc_id) }}" class="btn btn-sm btn-icon btn-light-primary" title="Editar Local">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.locais.destroy', $local->admlc_id) }}" method="POST" class="d-inline-block delete-local-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Excluir Local">
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
                        {{ $locais->appends(request()->query())->links() }}
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
        const regionalSelect = document.getElementById('admrg_id_select');
        if (regionalSelect) {
            regionalSelect.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        }
    });

    document.querySelectorAll('.delete-local-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Excluir Administração Local?',
                text: "Esta ação é irreversível e só será realizada se não houver usuários, igrejas ou dependências associadas!",
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
