@extends('layouts.app')

@section('title', 'Nova Regional')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-map-pin me-2"></i>Cadastrar Nova Administração Regional</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.regionais.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Código ID Regional</label>
                        <input type="number" name="admrg_id" class="form-control @error('admrg_id') is-invalid @enderror" value="{{ old('admrg_id') }}" placeholder="Ex: 1" required>
                        @error('admrg_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome da Administração Regional</label>
                        <input type="text" name="adm_regional" class="form-control @error('adm_regional') is-invalid @enderror" value="{{ old('adm_regional') }}" placeholder="Ex: Regional Curitiba" required>
                        @error('adm_regional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">UF (Estado)</label>
                        <input type="text" name="uf" class="form-control @error('uf') is-invalid @enderror" value="{{ old('uf') }}" placeholder="Ex: PR" maxlength="2" required style="text-transform: uppercase;">
                        @error('uf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.regionais.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-outline-info">
                            <i class="ti ti-device-floppy me-1"></i> Salvar Regional
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
