@extends('layouts.auth')

@section('title', 'Redefinir Senha')
@section('meta_description', 'Redefina a sua senha de acesso ao SIBEM Web - Sistema para Inventário de Bens Móveis.')
@section('sidebar_text', 'Crie uma nova senha segura para o seu login.')

@section('content')
<div class="card my-5 mx-3">
    <div class="card-header bg-dark">
        <h4 class="text-center text-white mb-0 f-w-500">Nova Senha</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group mb-3">
                <label class="form-label" for="email">Confirmar E-mail</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="{{ $email ?? old('email') }}" required autofocus>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="password">Nova Senha</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Nova Senha" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="password_confirmation">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirmar Senha" required>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
            </div>
        </form>
    </div>
    <div class="card-footer border-top text-center">
        <p class="mb-0 text-muted">© {{ date('Y') }} SIBEM CCB.</p>
    </div>
</div>
@endsection
