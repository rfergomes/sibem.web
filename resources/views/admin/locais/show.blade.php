@extends('layouts.app')

@section('title', 'Detalhes da Localidade')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-building me-2"></i>Dados da Administração Local</h4>
            </div>
            
            <div class="card-body">
                <table class="table table-sm table-striped align-middle">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">ID Local (Código):</td>
                            <td><span class="fw-bold text-dark">{{ $local->admlc_id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Administração Local:</td>
                            <td>{{ $local->adm_local }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Razão Social:</td>
                            <td>{{ $local->razao_social }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">CNPJ:</td>
                            <td><code>{{ $local->cnpj }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Cidade:</td>
                            <td>{{ $local->cidade }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Estado (UF):</td>
                            <td><span class="badge bg-light-primary text-primary">{{ $local->uf }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Regional Vinculada:</td>
                            <td>{{ $local->regional->adm_regional ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                @if($local->status_id == 1)
                                    <span class="badge bg-light-success text-success">Ativa</span>
                                @else
                                    <span class="badge bg-light-danger text-danger">Inativa</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 d-flex justify-content-between">
                    @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                        <a href="{{ route('admin.locais.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if(Auth::user()->isAdminSistema())
                        <a href="{{ route('admin.locais.edit', $local->admlc_id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i> Editar Localidade
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
