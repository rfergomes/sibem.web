@extends('layouts.app')

@section('title', 'Editar Servidor')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3" style="border-radius: 8px 8px 0 0;">
                <h4 class="mb-0 text-white"><i class="ti ti-server me-2"></i>Editar Configuração de Servidor</h4>
            </div>
            
            <div class="card-body p-4">
                @if($servidor->provisionado)
                    <div class="alert alert-warning border-warning d-flex align-items-center mb-4" role="alert">
                        <i class="ti ti-lock-square me-3 fs-3"></i>
                        <div>
                            <strong class="d-block">Servidor Provisionado e Bloqueado!</strong>
                            Este servidor de banco de dados já passou pelo processo de provisionamento das tabelas.
                            Por motivos de segurança e integridade de dados, os campos de conexão foram congelados contra edições.
                        </div>
                    </div>
                @endif

                <form action="{{ route('admin.servidores.update', $servidor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Administração Local -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Administração Local Vinculada</label>
                            @if($servidor->provisionado)
                                <input type="text" class="form-control bg-light" value="{{ $servidor->local->adm_local ?? '' }} (ID: {{ $servidor->admlc_id }})" disabled>
                                <input type="hidden" name="admlc_id" value="{{ $servidor->admlc_id }}">
                            @else
                                <select name="admlc_id" class="form-select @error('admlc_id') is-invalid @enderror" required>
                                    @foreach($locais as $local)
                                        <option value="{{ $local->admlc_id }}" {{ old('admlc_id', $servidor->admlc_id) == $local->admlc_id ? 'selected' : '' }}>
                                            {{ $local->adm_local }} (ID: {{ $local->admlc_id }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('admlc_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Descrição Identificadora</label>
                            <input type="text" name="descricao" class="form-control @error('descricao') is-invalid @enderror" value="{{ old('descricao', $servidor->descricao) }}" placeholder="Ex: Servidor Regional Curitiba" required>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Endereço do Servidor -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Endereço IP ou Hostname</label>
                            <input type="text" name="servidor" class="form-control @error('servidor') is-invalid @enderror {{ $servidor->provisionado ? 'bg-light' : '' }}" value="{{ old('servidor', $servidor->servidor) }}" placeholder="Ex: localhost ou 192.168.1.5" required {{ $servidor->provisionado ? 'disabled' : '' }}>
                            @error('servidor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Porta -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Porta de Conexão MySQL</label>
                            <input type="number" name="porta" class="form-control @error('porta') is-invalid @enderror {{ $servidor->provisionado ? 'bg-light' : '' }}" value="{{ old('porta', $servidor->porta) }}" placeholder="Ex: 3306" required {{ $servidor->provisionado ? 'disabled' : '' }}>
                            @error('porta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Nome do Banco de Dados -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nome do Banco de Dados (Schema)</label>
                            <input type="text" name="banco" class="form-control @error('banco') is-invalid @enderror {{ $servidor->provisionado ? 'bg-light' : '' }}" value="{{ old('banco', $servidor->banco) }}" placeholder="Ex: sibem_curitiba" required {{ $servidor->provisionado ? 'disabled' : '' }}>
                            @error('banco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Usuário MySQL -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Usuário de Acesso MySQL</label>
                            <input type="text" name="usuario" class="form-control @error('usuario') is-invalid @enderror {{ $servidor->provisionado ? 'bg-light' : '' }}" value="{{ old('usuario', $servidor->usuario) }}" placeholder="Ex: root" required {{ $servidor->provisionado ? 'disabled' : '' }}>
                            @error('usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Senha MySQL -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Senha de Acesso MySQL</label>
                            <div class="input-group">
                                <input type="password" name="senha" id="mysql-senha" class="form-control @error('senha') is-invalid @enderror {{ $servidor->provisionado ? 'bg-light' : '' }}" value="{{ old('senha', $servidor->senha) }}" placeholder="Em branco caso não possua senha" {{ $servidor->provisionado ? 'disabled' : '' }}>
                                @if(!$servidor->provisionado)
                                    <button type="button" class="btn btn-outline-secondary" id="toggle-senha-btn" onclick="togglePasswordVisibility()">
                                        <i class="ti ti-eye" id="toggle-senha-icon"></i>
                                    </button>
                                @endif
                            </div>
                            @error('senha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ativo Switch -->
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input no-choices" type="checkbox" name="ativo" id="server-ativo" value="1" {{ old('ativo', $servidor->ativo) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold ms-2" for="server-ativo">Servidor Ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between border-top pt-3">
                        <a href="{{ route('admin.servidores.index') }}" class="btn btn-light-secondary d-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i> Voltar para a Listagem
                        </a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-device-floppy me-1"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('mysql-senha');
        const icon = document.getElementById('toggle-senha-icon');
        
        if (passwordInput && passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else if (passwordInput) {
            passwordInput.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }
</script>
@endsection
