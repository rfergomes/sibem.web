@extends('layouts.app')

@section('title', 'Editar Igreja')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-edit me-2"></i>Editar Igreja: {{ $igreja->igreja }}</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.igrejas.update', $igreja->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="redirect_url" value="{{ $redirectUrl }}">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ID da Igreja (Cód. Legado)</label>
                            <input type="text" name="igreja_id" class="form-control @error('igreja_id') is-invalid @enderror" value="{{ old('igreja_id', $igreja->igreja_id) }}" required>
                            @error('igreja_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cód. SIGA</label>
                            <input type="text" name="cod_siga" class="form-control @error('cod_siga') is-invalid @enderror" value="{{ old('cod_siga', $igreja->cod_siga) }}" required>
                            @error('cod_siga')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Comum Congregação</label>
                            <input type="text" name="igreja" class="form-control @error('igreja') is-invalid @enderror" value="{{ old('igreja', $igreja->igreja) }}" required>
                            @error('igreja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Razão Social</label>
                            <input type="text" name="razao_social" class="form-control @error('razao_social') is-invalid @enderror" value="{{ old('razao_social', $igreja->razao_social) }}">
                            @error('razao_social')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj', $igreja->cnpj) }}">
                            @error('cnpj')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">Logradouro / Endereço</label>
                            <input type="text" name="logradouro" class="form-control @error('logradouro') is-invalid @enderror" value="{{ old('logradouro', $igreja->logradouro) }}">
                            @error('logradouro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número</label>
                            <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero', $igreja->numero) }}">
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Bairro</label>
                            <input type="text" name="bairro" class="form-control @error('bairro') is-invalid @enderror" value="{{ old('bairro', $igreja->bairro) }}">
                            @error('bairro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror" value="{{ old('cidade', $igreja->cidade) }}" required>
                            @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">UF (Estado)</label>
                            <input type="text" name="uf" class="form-control @error('uf') is-invalid @enderror" value="{{ old('uf', $igreja->uf) }}" maxlength="2" required style="text-transform: uppercase;">
                            @error('uf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipo de Imóvel</label>
                            <select name="tipo_id" class="form-select @error('tipo_id') is-invalid @enderror">
                                <option value="">Selecione o tipo...</option>
                                @foreach($tiposImovel as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_id', $igreja->tipo_id) == $tipo->id ? 'selected' : '' }}>{{ $tipo->nome }}</option>
                                @endforeach
                            </select>
                            @error('tipo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Código do Setor</label>
                            <select name="cod_setor" class="form-select @error('cod_setor') is-invalid @enderror">
                                <option value="">Selecione o setor...</option>
                                @foreach($setores as $setor)
                                    <option value="{{ $setor->cod_setor }}" {{ old('cod_setor', $igreja->cod_setor) == $setor->cod_setor ? 'selected' : '' }}>{{ $setor->cod_setor }} - {{ $setor->descricao }}</option>
                                @endforeach
                            </select>
                            @error('cod_setor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Administração Local Vinculada</label>
                            <select name="admlc_id" class="form-select @error('admlc_id') is-invalid @enderror" required>
                                <option value="">Selecione a localidade...</option>
                                @foreach($locais as $local)
                                    <option value="{{ $local->admlc_id }}" {{ old('admlc_id', $igreja->admlc_id) == $local->admlc_id ? 'selected' : '' }}>{{ $local->nome }}</option>
                                @endforeach
                            </select>
                            @error('admlc_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status_id" class="form-select @error('status_id') is-invalid @enderror" required>
                                <option value="1" {{ old('status_id', $igreja->status_id) == 1 ? 'selected' : '' }}>Ativo</option>
                                <option value="0" {{ old('status_id', $igreja->status_id) == 0 ? 'selected' : '' }}>Inativo</option>
                            </select>
                            @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Observação</label>
                            <textarea name="observacao" class="form-control @error('observacao') is-invalid @enderror" rows="2">{{ old('observacao', $igreja->observacao) }}</textarea>
                            @error('observacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ $redirectUrl ?? route('admin.igrejas.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-device-floppy me-1"></i> Atualizar Igreja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
