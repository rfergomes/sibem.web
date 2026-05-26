@extends('layouts.app')

@section('title', 'Detalhes do Setor')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-chart-bar me-2"></i>Dados do Setor</h4>
            </div>
            
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">ID:</td>
                            <td><span class="fw-bold text-dark">{{ $setor->id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Código:</td>
                            <td><span class="badge bg-light-info text-info fw-bold">{{ $setor->cod_setor }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Descrição:</td>
                            <td>{{ $setor->descricao }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Administração Local:</td>
                            <td>{{ $setor->local->nome ?? 'N/A' }}</td>
                        </tr>
                        @if($setor->local && $setor->local->regional)
                            <tr>
                                <td class="fw-bold text-muted">Regional:</td>
                                <td>{{ $setor->local->regional->adm_regional }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.setores.index') }}" class="btn btn-light-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Voltar
                    </a>

                    @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                        <a href="{{ route('admin.setores.edit', $setor->id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i> Editar Setor
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
