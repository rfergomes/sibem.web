@extends('layouts.error')

@section('title', 'Página Não Encontrada')

@section('content')
    <div class="auth-main v1">
        <div class="auth-wrapper">
            <div class="auth-form text-center">
                <div class="card my-5 mx-auto" style="max-width: 500px;">
                    <div class="card-body">
                        <h1 class="text-primary mt-4" style="font-size: 100px; font-weight: 800; line-height: 1;">404</h1>
                        <h4 class="mb-3">Página Não Encontrada</h4>
                        <p class="text-muted mb-4">Desculpe, a página que você está procurando não existe ou foi movida.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary mb-4">Ir para o Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
