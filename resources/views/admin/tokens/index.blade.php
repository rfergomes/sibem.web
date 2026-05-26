@extends('layouts.app')

@section('title', 'Gerenciamento de Tokens Desktop')

@section('content')
<div class="row">
    <!-- Generate Token Card -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0 text-white"><i class="ti ti-key me-2"></i>Gerar Novo Token</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tokens.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Associar ao Usuário</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">Selecione o Usuário...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->local->nome ?? 'Sem Localidade' }})</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Descrição da Máquina / Computador</label>
                        <input type="text" name="dispositivo" class="form-control @error('dispositivo') is-invalid @enderror" placeholder="Ex: Recepção, Caixa, Notebook Ti" required>
                        @error('dispositivo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="ti ti-plus me-1"></i> Gerar Token Ativo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Tokens List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark d-flex align-items-center justify-content-between">
                <h4 class="mb-0 text-white"><i class="ti ti-list me-2"></i>Tokens Desktop Ativos</h4>
            </div>
            
            <div class="card-body">
                <form action="{{ route('admin.tokens.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar por token, máquina, usuário ou e-mail..." value="{{ request('search') }}">
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
                                    <th>Administração Local</th>
                                    <th>Máquina / Dispositivo</th>
                                    <th>Token</th>
                                    <th>Gerado em</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokens as $token)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $token->user->name ?? 'N/A' }}</span>
                                            <small class="text-muted d-block">{{ $token->user->email ?? '' }}</small>
                                        </td>
                                        <td>{{ $token->local->nome ?? 'Sem Localidade' }}</td>
                                        <td>
                                            <code>{{ $token->dispositivo }}</code>
                                        </td>
                                        <td>
                                            <code>{{ $token->token }}</code>
                                        </td>
                                        <td>
                                            <small>{{ $token->created_at ? $token->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.tokens.destroy', $token->id) }}" method="POST" class="d-inline-block revoke-token-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger" title="Revogar Token">
                                                    <i class="ti ti-power me-1"></i> Revogar
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
</script>
@endsection
