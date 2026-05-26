@extends('layouts.app')

@section('title', 'Detalhes da Igreja (CCB)')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-white"><i class="ti ti-building-community me-2"></i>Templo CCB: {{ $igreja->igreja }}</h4>
                <span class="badge bg-light-primary text-primary fw-bold">{{ $igreja->cod_siga }}</span>
            </div>
            
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 35%;">ID Igreja (Cód. Legado):</td>
                            <td><span class="fw-bold text-dark">{{ $igreja->igreja_id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Cód. SIGA:</td>
                            <td><span class="fw-bold text-primary">{{ $igreja->cod_siga }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Comum Congregação:</td>
                            <td>{{ $igreja->igreja }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tipo de Imóvel:</td>
                            <td>
                                @if($igreja->tipoImovel)
                                    <span class="badge bg-light-info text-info fw-bold">{{ $igreja->tipoImovel->nome }}</span>
                                @else
                                    <span class="text-muted">Não definido</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Razão Social:</td>
                            <td>{{ $igreja->razao_social ?? 'Não informada' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">CNPJ:</td>
                            <td><code>{{ $igreja->cnpj ?? 'Não informado' }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Endereço:</td>
                            <td>
                                @if($igreja->logradouro)
                                    {{ $igreja->logradouro }}, {{ $igreja->numero ?? 'S/N' }}
                                    @if($igreja->bairro) - Bairro: {{ $igreja->bairro }} @endif
                                @else
                                    <span class="text-muted">Endereço não informado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Cidade / UF:</td>
                            <td>{{ $igreja->cidade ?? 'N/A' }} / {{ $igreja->uf ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Código Setor:</td>
                            <td>{{ $igreja->cod_setor ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Administração Local:</td>
                            <td>{{ $igreja->local->nome ?? 'N/A' }}</td>
                        </tr>
                        @if($igreja->local && $igreja->local->regional)
                            <tr>
                                <td class="fw-bold text-muted">Administração Regional:</td>
                                <td>{{ $igreja->local->regional->adm_regional }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                @if($igreja->status_id == 1)
                                    <span class="badge bg-light-success text-success">Ativo</span>
                                @else
                                    <span class="badge bg-light-danger text-danger">Inativo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Observação:</td>
                            <td>{{ $igreja->observacao ?? 'Nenhuma observação cadastrada' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.igrejas.index') }}" class="btn btn-light-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Voltar
                    </a>

                    @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional() || Auth::user()->isAdminLocal())
                        <a href="{{ route('admin.igrejas.edit', $igreja->id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i> Editar Templo
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
