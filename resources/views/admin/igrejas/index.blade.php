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
                <form action="{{ route('admin.igrejas.index') }}" method="GET" class="row g-3 mb-4" id="filter-form">
                    <div class="col-md-5 col-lg-4">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por comum, siga, cidade..." value="{{ request('search') }}">
                    </div>
                    @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                        <div class="col-md-4 col-lg-3">
                            <select name="admlc_id" class="form-select no-choices" onchange="document.getElementById('filter-form').submit()">
                                <option value="">-- Todas as Administrações --</option>
                                @foreach($availableLocais as $localOpt)
                                    <option value="{{ $localOpt->admlc_id }}" {{ request('admlc_id') == $localOpt->admlc_id ? 'selected' : '' }}>
                                        {{ $localOpt->adm_local }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="ti ti-search"></i> Filtrar
                        </button>
                        @if(request('search') || request('admlc_id'))
                            <a href="{{ route('admin.igrejas.index') }}" class="btn btn-outline-danger">Limpar</a>
                        @endif
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
                                    <th>Casa de Oração / Localidade</th>
                                    <th>Tipo de Imóvel</th>
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
                                            <span class="fw-bold text-dark">{{ $igreja->igreja }}
                                                <span class="d-block mt-1 text-muted">Setor: @if($igreja->setor_id) {{$igreja->setor_id}} @else Não informado @endif</span>
                                                
                                            </span>
                                        </td>
                                        <td>
                                            @if($igreja->tipoImovel)
                                                <span class="badge bg-light-primary text-primary">{{ $igreja->tipoImovel->nome }}</span>
                                            @else
                                                <span class="text-muted">Não definido</span>
                                            @endif
                                        </td>
                                        <td>{{ $igreja->cidade ?? 'N/A' }} / {{ $igreja->uf ?? 'N/A' }}
                                            <small class="d-block text-muted">{{ $igreja->local->adm_local ?? 'N/A' }}</small>
                                        </td>
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
