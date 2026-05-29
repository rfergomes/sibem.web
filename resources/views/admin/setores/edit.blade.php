@extends('layouts.app')

@section('title', 'Editar Setor')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-edit me-2"></i>Editar Setor: {{ $setor->descricao }}</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.setores.update', $setor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código do Setor (3 dígitos)</label>
                        <input type="text" name="cod_setor" class="form-control @error('cod_setor') is-invalid @enderror" value="{{ old('cod_setor', $setor->cod_setor) }}" maxlength="3" required>
                        @error('cod_setor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição / Nome do Setor</label>
                        <input type="text" name="descricao" class="form-control @error('descricao') is-invalid @enderror" value="{{ old('descricao', $setor->descricao) }}" required>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Administração Local Vinculada</label>
                        <select name="admlc_id" class="form-select @error('admlc_id') is-invalid @enderror" required>
                            <option value="">Selecione a localidade...</option>
                            @foreach($locais as $local)
                                <option value="{{ $local->admlc_id }}" {{ old('admlc_id', $setor->admlc_id) == $local->admlc_id ? 'selected' : '' }}>{{ $local->nome }}</option>
                            @endforeach
                        </select>
                        @error('admlc_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.setores.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-outline-info">
                            <i class="ti ti-device-floppy me-1"></i> Atualizar Setor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
