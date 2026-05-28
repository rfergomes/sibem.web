@extends('layouts.auth')

@section('title', 'Login')
@section('meta_description', 'Acesse o SIBEM Web - Sistema para Inventário de Bens Móveis da Congregação Cristã no Brasil.')
@section('sidebar_text', 'SIBEM CCB - Sistema para Inventário de Bens Móveis. Gerenciamento unificado e controle de acesso integrado.')

@section('content')
<div class="card my-5 mx-3">
    <div class="card-header bg-dark">
        <h4 class="text-center text-white mb-0 f-w-500">Acesse o Sistema</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Error Alert -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="form-group mb-3">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label" for="password">Senha</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Senha" required>
            </div>

            <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" name="remember" checked>
                    <label class="form-check-label text-muted" for="customCheckc1">Lembrar-me</label>
                </div>
                <a href="{{ route('password.request') }}" class="text-muted">Esqueceu a senha?</a>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
        </form>
    </div>
    <div class="card-footer border-top text-center">
        <p class="mb-0 text-muted">© {{ date('Y') }} SIBEM CCB. Todos os direitos reservados.</p>
    </div>
</div>
@endsection
