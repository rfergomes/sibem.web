@extends('layouts.app')

@section('title', 'Gerenciamento de Tokens Desktop')

@section('content')
<div class="row">
    <!-- Active Tokens List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-list me-2"></i>Tokens Desktop Ativos</h4>
                <button type="button" class="btn btn-light-secondary" data-bs-toggle="modal" data-bs-target="#gerarTokenModal">
                    <i class="ti ti-plus me-1"></i> Gerar Novo Token
                </button>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.tokens.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por token, máquina, usuário ou e-mail..." value="{{ request('search') }}" onchange="this.form.submit()">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="ti ti-search"></i> Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.tokens.index') }}" class="btn btn-outline-danger">Limpar</a>
                            @endif
                        </div>
                    </div>
                </form>

                @if($tokens->isEmpty())
                    <div class="text-center p-5">
                        <i class="ti ti-key text-muted" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Nenhum token ativo cadastrado ou encontrado</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuário</th>
                                    <th>Administração</th>
                                    <th>Token / Dispositivo</th>
                                    <th class="text-end" style="min-width: 280px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokens as $token)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $token->user->name ?? 'N/A' }}</span>
                                            <small class="text-muted d-block"><i class="ti ti-mail me-1"></i> {{ $token->user->email ?? '' }}</small>
                                        </td>
                                        <td>{{ $token->local->nome ?? 'Sem Localidade' }}</td>
                                        <td>
                                            <code class="d-block mt-1"><i class="ti ti-key me-1"></i>{{ $token->token }}</code>
                                            <small class="d-block mt-1"><i class="ti ti-device-desktop me-1"></i>{{ $token->dispositivo }} - Gerado: {{ $token->created_at ? $token->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                        </td>
                                        <td class="text-end">
                                            @if($token->user && $token->user->email)
                                                <form action="{{ route('admin.tokens.send-email', $token->id) }}" method="POST" class="d-inline-block">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light-primary" title="Enviar por E-mail">
                                                        <i class="ti ti-mail"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if($token->user && $token->user->telefone)
                                                @php
                                                    $telefoneLimpo = preg_replace('/\D/', '', $token->user->telefone);
                                                    if (strlen($telefoneLimpo) > 0 && !str_starts_with($telefoneLimpo, '55') && strlen($telefoneLimpo) <= 11) {
                                                        $telefoneLimpo = '55' . $telefoneLimpo;
                                                    }
                                                    $msg = "A Paz de Deus!\nSegue token para acesso ao sistema de inventários SIBEM CCB\n\n" . $token->token;
                                                    $msgUrl = rawurlencode($msg);
                                                @endphp
                                                @if(strlen($telefoneLimpo) > 0)
                                                    <a href="https://wa.me/{{ $telefoneLimpo }}?text={{ $msgUrl }}" target="_blank" class="btn btn-sm btn-light-success" title="Enviar por WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                @endif
                                            @endif

                                            <form action="{{ route('admin.tokens.destroy', $token->id) }}" method="POST" class="d-inline-block revoke-token-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger" title="Revogar Token">
                                                    <i class="ti ti-power"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer d-flex justify-content-end border-top-0 bg-transparent">
                        {{ $tokens->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Gerar Novo Token -->
<div class="modal fade" id="gerarTokenModal" tabindex="-1" aria-labelledby="gerarTokenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title text-white" id="gerarTokenModalLabel"><i class="ti ti-key me-2"></i>Gerar Novo Token</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.tokens.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Associar ao Usuário</label>
                        <select name="user_id" class="form-select no-choices @error('user_id') is-invalid @enderror" required>
                            <option value="">Selecione o Usuário...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->local->nome ?? 'Sem Localidade' }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Administração</label>
                        <select name="admlc_id" class="form-select no-choices @error('admlc_id') is-invalid @enderror" required>
                            <option value="">Selecione a Administração...</option>
                            @foreach($locais as $local)
                                <option value="{{ $local->admlc_id }}" {{ old('admlc_id') == $local->admlc_id ? 'selected' : '' }}>
                                    {{ $local->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('admlc_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição da Máquina / Computador</label>
                        <input type="text" name="dispositivo" class="form-control @error('dispositivo') is-invalid @enderror" placeholder="Ex: Recepção, Caixa, Notebook Ti" value="{{ old('dispositivo') }}" required>
                        @error('dispositivo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Gerar Token Ativo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.revoke-token-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Revogar Token?',
                text: "O aplicativo desktop nesta máquina perderá o acesso de escrita aos bancos de dados locais imediatamente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f15bb5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, revogar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Auto-open modal on validation errors
    @if($errors->has('user_id') || $errors->has('admlc_id') || $errors->has('dispositivo'))
    document.addEventListener('DOMContentLoaded', function () {
        var myModal = new bootstrap.Modal(document.getElementById('gerarTokenModal'));
        myModal.show();
    });
    @endif
</script>
@endsection
