@extends('layouts.app')

@section('title', 'Detalhes da Dependência')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-folder me-2"></i>Dados da Dependência Global</h4>
            </div>
            
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <tbody>
                        <tr>
                            <td class="fw-bold text-muted" style="width: 40%;">ID Dependência:</td>
                            <td><span class="fw-bold text-dark">{{ $dependencia->dependencia_id }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Descrição:</td>
                            <td>{{ $dependencia->descricao }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Cadastrado em:</td>
                            <td>{{ $dependencia->created_at ? $dependencia->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.dependencias.index') }}" class="btn btn-light-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Voltar
                    </a>

                    @if(Auth::user()->isAdminSistema())
                        <a href="{{ route('admin.dependencias.edit', $dependencia->id) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i> Editar Dependência
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
