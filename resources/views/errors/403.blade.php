@extends('layouts.error')

@section('title', 'Acesso Negado')

@section('code', '403')

@section('message', 'Acesso Negado')

@section('description')
    Você não tem permissão para acessar esta página.
    Se você acredita que isso é um erro, por favor, contate o administrador do sistema.
@endsection

@section('actions')
    <a href="{{ url('/') }}"
        class="inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:text-lg">
        Voltar para a Página Inicial
    </a>
@endsection
