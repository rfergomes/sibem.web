@extends('layouts.app')

@section('title', 'Nova Localidade')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-building-plus me-2"></i>Cadastrar Nova Administração Local</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.locais.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Código ID Local</label>
                            <input type="number" name="admlc_id" class="form-control @error('admlc_id') is-invalid @enderror" value="{{ old('admlc_id') }}" placeholder="Ex: 22" required>
                            @error('admlc_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nome da Administração Local</label>
                            <input type="text" name="adm_local" class="form-control @error('adm_local') is-invalid @enderror" value="{{ old('adm_local') }}" placeholder="Ex: Curitiba Sul" required>
                            @error('adm_local')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Razão Social</label>
                            <input type="text" name="razao_social" class="form-control @error('razao_social') is-invalid @enderror" value="{{ old('razao_social') }}" placeholder="Ex: Congregação Cristã no Brasil" required>
                            @error('razao_social')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj') }}" placeholder="Ex: 00.000.000/0000-00" required>
                            @error('cnpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror" value="{{ old('cidade') }}" required>
                            @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">UF (Estado)</label>
                            <input type="text" name="uf" class="form-control @error('uf') is-invalid @enderror" value="{{ old('uf') }}" maxlength="2" required style="text-transform: uppercase;">
                            @error('uf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Administração Regional Vinculada</label>
                            <select name="admrg_id" class="form-select @error('admrg_id') is-invalid @enderror" required>
                                <option value="">Selecione a Regional...</option>
                                @foreach($regionais as $reg)
                                    <option value="{{ $reg->admrg_id }}" {{ old('admrg_id') == $reg->admrg_id ? 'selected' : '' }}>{{ $reg->adm_regional }}</option>
                                @endforeach
                            </select>
                            @error('admrg_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status ID</label>
                            <select name="status_id" class="form-select @error('status_id') is-invalid @enderror" required>
                                <option value="1" {{ old('status_id') == 1 ? 'selected' : '' }}>Ativa</option>
                                <option value="0" {{ old('status_id') == 0 ? 'selected' : '' }}>Inativa</option>
                            </select>
                            @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.locais.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Salvar Localidade
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
