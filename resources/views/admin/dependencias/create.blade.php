@extends('layouts.app')

@section('title', 'Nova Dependência')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-plus me-2"></i>Cadastrar Nova Dependência Global</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.dependencias.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código ID Dependência (Único)</label>
                        <input type="number" name="dependencia_id" class="form-control @error('dependencia_id') is-invalid @enderror" value="{{ old('dependencia_id') }}" placeholder="Ex: 5" required>
                        @error('dependencia_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição da Dependência</label>
                        <input type="text" name="descricao" class="form-control @error('descricao') is-invalid @enderror" value="{{ old('descricao') }}" placeholder="Ex: Nave Central" required>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.dependencias.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-outline-info">
                            <i class="ti ti-device-floppy me-1"></i> Salvar Dependência
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
