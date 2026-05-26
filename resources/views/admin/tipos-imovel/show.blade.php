@extends('layouts.app')

@section('title', 'Detalhes do Tipo de Imóvel')

@section('content')
<div class="row">
    <!-- Property Type Details -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-folder me-2"></i>Dados do Tipo</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted">ID Tipo:</td>
                            <td>{{ $tipoImovel->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Nome:</td>
                            <td><span class="fw-bold text-dark">{{ $tipoImovel->nome }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Criado em:</td>
                            <td><small>{{ $tipoImovel->created_at ? $tipoImovel->created_at->format('d/m/Y H:i') : 'N/A' }}</small></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4 text-center">
                    @if(Auth::user()->isAdminSistema())
                        <a href="{{ route('admin.tipos-imovel.edit', $tipoImovel->id) }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-edit"></i> Editar
                        </a>
                    @endif
                    <a href="{{ route('admin.tipos-imovel.index') }}" class="btn btn-light-secondary btn-sm ms-2">
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Associated Churches -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-list me-2"></i>Igrejas Vinculadas</h4>
            </div>
            <div class="card-body p-0">
                @if($tipoImovel->igrejas->isEmpty())
                    <div class="text-center p-5">
                        <p class="text-muted mb-0">Nenhuma igreja atualmente associada a este tipo de imóvel.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Cód. SIGA</th>
                                    <th>Igreja</th>
                                    <th>Administração Local</th>
                                    <th>Cidade</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipoImovel->igrejas as $igreja)
                                    <tr>
                                        <td><span class="badge bg-light-primary text-primary fw-bold">{{ $igreja->cod_siga }}</span></td>
                                        <td><span class="fw-bold text-dark">{{ $igreja->igreja }}</span></td>
                                        <td>{{ $igreja->local->nome ?? 'N/A' }}</td>
                                        <td>{{ $igreja->cidade }} ({{ $igreja->uf }})</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.igrejas.show', $igreja->id) }}" class="btn btn-sm btn-icon btn-light-info" title="Ver Detalhes">
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
