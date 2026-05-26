@extends('layouts.app')

@section('title', 'Detalhes da Regional')

@section('content')
<div class="row">
    <!-- Regional Details -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-map-pin me-2"></i>Dados da Regional</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted">ID Regional:</td>
                            <td>{{ $regional->admrg_id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Nome:</td>
                            <td>{{ $regional->adm_regional }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">UF:</td>
                            <td><span class="badge bg-light-primary text-primary">{{ $regional->uf }}</span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.regionais.edit', $regional->id) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-edit"></i> Editar Regional
                    </a>
                    <a href="{{ route('admin.regionais.index') }}" class="btn btn-light-secondary btn-sm ms-2">
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Locales in this regional -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-list me-2"></i>Administrações Locais Vinculadas</h4>
            </div>
            <div class="card-body p-0">
                @if($regional->locais->isEmpty())
                    <div class="text-center p-5">
                        <p class="text-muted mb-0">Nenhuma Administração Local vinculada a esta regional.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Local</th>
                                    <th>Administração Local</th>
                                    <th>Razão Social</th>
                                    <th>CNPJ</th>
                                    <th>Cidade</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regional->locais as $local)
                                    <tr>
                                        <td>{{ $local->admlc_id }}</td>
                                        <td><span class="fw-bold text-dark">{{ $local->adm_local }}</span></td>
                                        <td>{{ $local->razao_social }}</td>
                                        <td>{{ $local->cnpj }}</td>
                                        <td>{{ $local->cidade }} ({{ $local->uf }})</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.locais.show', $local->admlc_id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
