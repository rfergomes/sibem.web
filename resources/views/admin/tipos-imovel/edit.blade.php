@extends('layouts.app')

@section('title', 'Editar Tipo de Imóvel')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-edit me-2"></i>Editar Tipo de Imóvel: {{ $tipoImovel->nome }}</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.tipos-imovel.update', $tipoImovel->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome do Tipo de Imóvel</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $tipoImovel->nome) }}" required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.tipos-imovel.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-device-floppy me-1"></i> Atualizar Tipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
