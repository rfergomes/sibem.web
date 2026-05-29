@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-user-edit me-2"></i>Editar Usuário: {{ $usuario->name }}</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nome Completo</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">E-mail</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Telefone</label>
                            <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $usuario->telefone) }}" placeholder="(99) 99999-9999">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Perfil de Acesso</label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="">Selecione um Perfil...</option>
                                @if(Auth::user()->isAdminSistema())
                                    <option value="admin_sistema" {{ old('tipo', $usuario->tipo) === 'admin_sistema' ? 'selected' : '' }}>Super Admin (Sistema)</option>
                                    <option value="admin_regional" {{ old('tipo', $usuario->tipo) === 'admin_regional' ? 'selected' : '' }}>Admin Regional</option>
                                @endif
                                @if(Auth::user()->isAdminSistema() || Auth::user()->isAdminRegional())
                                    <option value="admin_local" {{ old('tipo', $usuario->tipo) === 'admin_local' ? 'selected' : '' }}>Admin Local</option>
                                @endif
                                <option value="operador" {{ old('tipo', $usuario->tipo) === 'operador' ? 'selected' : '' }}>Operador</option>
                                <option value="auditor" {{ old('tipo', $usuario->tipo) === 'auditor' ? 'selected' : '' }}>Auditor</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Administração Local</label>
                            <select name="admlc_id" class="form-select @error('admlc_id') is-invalid @enderror" required>
                                <option value="">Selecione uma Administração...</option>
                                @foreach($locais as $local)
                                    <option value="{{ $local->admlc_id }}" {{ old('admlc_id', $usuario->admlc_id) == $local->admlc_id ? 'selected' : '' }}>{{ $local->nome }}</option>
                                @endforeach
                            </select>
                            @error('admlc_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Comum Congregação</label>
                            <input type="text" name="igreja" class="form-control @error('igreja') is-invalid @enderror" value="{{ old('igreja', $usuario->igreja) }}">
                            @error('igreja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror" value="{{ old('cidade', $usuario->cidade) }}">
                            @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-1"></i> Deixe os campos de senha em branco se não desejar alterar a senha atual do usuário.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nova Senha</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Senha opcional...">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirmar Nova Senha</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repita a senha...">
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Atualizar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
