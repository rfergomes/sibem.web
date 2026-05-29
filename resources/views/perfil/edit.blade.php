@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="row">
    <!-- Coluna da Esquerda: Avatar e Informações de Acesso -->
    <div class="col-lg-4">
        <!-- Card do Avatar -->
        <div class="card mb-4 text-center">
            <div class="card-body">
                <div class="mb-4 position-relative d-inline-block">
                    <div id="avatar-container" class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background-color: #033D60;">
                        @if($user->foto)
                            <img id="avatar-preview" src="{{ asset('storage/' . $user->foto) }}?v={{ time() }}" alt="Foto do perfil" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <img id="avatar-preview" src="" alt="Foto do perfil" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                            <div id="avatar-initials" class="text-white fw-bold fs-2">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                </div>
                <h4 class="mb-1 text-dark fw-bold">{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                <span class="badge bg-light-primary text-primary px-3 py-2 fs-6 rounded-pill">
                    {{ match($user->tipo) {
                        'admin_sistema' => 'Super Admin',
                        'admin_regional' => 'Admin Regional',
                        'admin_local' => 'Admin Local',
                        'operador' => 'Operador',
                        'auditor' => 'Auditor',
                        default => $user->tipo
                    } }}
                </span>
            </div>
        </div>

        <!-- Card de Informações Administrativas (Somente Leitura) -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-white fw-bold"><i class="ti ti-shield-lock me-2"></i>Credenciais de Acesso</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-borderless">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="px-4 py-3 fw-bold text-muted" style="width: 45%;">Administração:</td>
                                <td class="px-4 py-3 text-dark fw-bold">{{ $user->local->nome ?? 'Nenhum' }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 fw-bold text-muted">Perfil</td>
                                <td class="px-4 py-3 text-dark fw-bold">
                                    {{ match($user->tipo) {
                                        'admin_sistema' => 'Super Admin',
                                        'admin_regional' => 'Admin Regional',
                                        'admin_local' => 'Admin Local',
                                        'operador' => 'Operador',
                                        'auditor' => 'Auditor',
                                        default => $user->tipo
                                    } }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card de Tokens Ativos -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0 text-white"><i class="ti ti-key me-2"></i>Tokens Ativos (Desktop)</h5>
            </div>
            <div class="card-body p-0">
                @if($tokens->isEmpty())
                    <div class="text-center p-4">
                        <i class="ti ti-key text-muted" style="font-size: 24px;"></i>
                        <p class="text-muted small mt-2 mb-0">Nenhum token desktop ativo.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($tokens as $token)
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-dark text-truncate" style="max-width: 70%;" title="{{ $token->local->nome ?? 'Sem Administração' }}">
                                        <i class="ti ti-building me-1"></i>{{ $token->local->nome ?? 'Sem Administração' }}
                                    </span>
                                    <span class="badge bg-light-success text-success">Ativo</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2 bg-light p-1 px-2 rounded" style="border: 1px dashed #dee2e6;">
                                    <div class="text-muted small text-truncate me-2" style="font-family: monospace; font-size: 11px;" title="{{ $token->token }}">
                                        {{ $token->token }}
                                    </div>
                                    <button type="button" class="btn btn-sm btn-icon btn-link text-primary p-0 m-0 d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; border: none; background: none;" onclick="copyToken('{{ $token->token }}', this)" title="Copiar Token">
                                        <i class="ti ti-copy" style="font-size: 16px;"></i>
                                    </button>
                                </div>
                                <div class="text-muted small">
                                    <i class="ti ti-device-laptop me-1 text-muted"></i>{{ $token->dispositivo }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Coluna da Direita: Edição dos Dados e Senha -->
    <div class="col-lg-8">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Card de Dados Pessoais -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-white"><i class="ti ti-user me-2"></i>Dados Pessoais</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nome Completo</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">E-mail</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Telefone</label>
                            <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $user->telefone) }}" placeholder="(99) 99999-9999">
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Comum Congregação</label>
                            <input type="text" name="igreja" class="form-control @error('igreja') is-invalid @enderror" value="{{ old('igreja', $user->igreja) }}" placeholder="Ex: Jd Nova América">
                            @error('igreja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cidade</label>
                            <input type="text" name="cidade" class="form-control @error('cidade') is-invalid @enderror" value="{{ old('cidade', $user->cidade) }}" placeholder="Ex: Campinas">
                            @error('cidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Foto de Perfil</label>
                            <div class="input-group">
                                <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                                @error('foto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG ou GIF. Tamanho máximo: 2MB.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Alteração de Senha -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-white"><i class="ti ti-lock me-2"></i>Alterar Senha</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info py-2">
                        <i class="ti ti-info-circle me-1"></i> Deixe os campos abaixo em branco se não desejar alterar sua senha atual.
                    </div>
                    
                    <div class="row g-3">
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
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-light-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Voltar ao Dashboard
                </a>
                <button type="submit" class="btn btn-outline-info px-4">
                    <i class="ti ti-device-floppy me-1"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Real-time Preview da Foto de Perfil
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Verificar tipo
            if (!file.type.match('image.*')) {
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                const initials = document.getElementById('avatar-initials');
                
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                
                if (initials) {
                    initials.classList.add('d-none');
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Função para Copiar o Token para a Área de Transferência
    function copyToken(text, button) {
        navigator.clipboard.writeText(text).then(function() {
            // Modifica o ícone temporariamente
            const icon = button.querySelector('i');
            icon.className = 'ti ti-check text-success';
            
            // Exibe mensagem rápida tipo Toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: false
            });
            Toast.fire({
                icon: 'success',
                title: 'Token copiado com sucesso!'
            });

            setTimeout(function() {
                icon.className = 'ti ti-copy';
            }, 2000);
        }, function(err) {
            console.error('Erro ao copiar token: ', err);
        });
    }
</script>
@endsection
