@extends('layouts.auth')

@section('title', 'Recuperar Senha')
@section('meta_description', 'Recupere seu acesso ao SIBEM Web - Sistema para Inventário de Bens Móveis.')
@section('sidebar_text', 'Recupere seu acesso ao SIBEM de forma simples e rápida.')

@section('content')
<div class="card my-5 mx-3">
    <div class="card-header bg-dark">
        <h4 class="text-center text-white mb-0 f-w-500">Recuperação de Senha</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('password.email') }}" method="POST">
            @csrf

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
                <label class="form-label" for="email">E-mail Cadastrado</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nome@exemplo.com" value="{{ old('email') }}" required autofocus>
                <small class="text-muted mt-1 d-block">Enviaremos um link de redefinição para a sua caixa de entrada.</small>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Enviar Link de Recuperação</button>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="link-primary">Voltar para o Login</a>
            </div>
        </form>
    </div>
    <div class="card-footer border-top text-center">
        <p class="mb-0 text-muted">© {{ date('Y') }} SIBEM CCB.</p>
    </div>
</div>
@endsection
