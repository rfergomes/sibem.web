@extends('layouts.app')

@section('title', 'Novo Servidor')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3" style="border-radius: 8px 8px 0 0;">
                <h4 class="mb-0 text-white"><i class="ti ti-server me-2"></i>Cadastrar Configuração de Servidor Local</h4>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('admin.servidores.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Administração Local -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Administração Local Vinculada</label>
                            <select name="admlc_id" class="form-select @error('admlc_id') is-invalid @enderror" required>
                                <option value="">Selecione uma Administração Local...</option>
                                @foreach($locais as $local)
                                    <option value="{{ $local->admlc_id }}" {{ old('admlc_id') == $local->admlc_id ? 'selected' : '' }}>
                                        {{ $local->adm_local }} (ID: {{ $local->admlc_id }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Apenas Administrações Locais sem servidores configurados aparecem listadas.</small>
                            @error('admlc_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Descrição Identificadora</label>
                            <input type="text" name="descricao" class="form-control @error('descricao') is-invalid @enderror" value="{{ old('descricao') }}" placeholder="Ex: Servidor Regional Curitiba" required>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Endereço do Servidor -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Endereço IP ou Hostname</label>
                            <input type="text" name="servidor" class="form-control @error('servidor') is-invalid @enderror" value="{{ old('servidor', '127.0.0.1') }}" placeholder="Ex: localhost ou 192.168.1.5" required>
                            @error('servidor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Porta -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Porta de Conexão MySQL</label>
                            <input type="number" name="porta" class="form-control @error('porta') is-invalid @enderror" value="{{ old('porta', '3306') }}" placeholder="Ex: 3306" required>
                            @error('porta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Nome do Banco de Dados -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nome do Banco de Dados (Schema)</label>
                            <input type="text" name="banco" class="form-control @error('banco') is-invalid @enderror" value="{{ old('banco', 'sibem_cps') }}" placeholder="Ex: sibem_curitiba" required>
                            @error('banco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Usuário MySQL -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Usuário de Acesso MySQL</label>
                            <input type="text" name="usuario" class="form-control @error('usuario') is-invalid @enderror" value="{{ old('usuario', 'root') }}" placeholder="Ex: root" required>
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
                                <input type="password" name="senha" id="mysql-senha" class="form-control @error('senha') is-invalid @enderror" value="{{ old('senha') }}" placeholder="Em branco caso não possua senha">
                                <button type="button" class="btn btn-outline-secondary" id="toggle-senha-btn" onclick="togglePasswordVisibility()">
                                    <i class="ti ti-eye" id="toggle-senha-icon"></i>
                                </button>
                            </div>
                            @error('senha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Ativo Switch -->
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input no-choices" type="checkbox" name="ativo" id="server-ativo" value="1" checked>
                                <label class="form-check-label fw-bold ms-2" for="server-ativo">Servidor Ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between border-top pt-3">
                        <a href="{{ route('admin.servidores.index') }}" class="btn btn-light-secondary d-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i> Voltar para a Listagem
                        </a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-device-floppy me-1"></i> Cadastrar Servidor
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
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            passwordInput.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }
</script>
@endsection
