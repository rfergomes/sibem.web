@extends('layouts.error')

@section('title', 'Erro Interno do Servidor')

@section('code', '500')

@section('message', 'Erro Interno do Servidor')

@section('description')
    Desculpe, algo deu errado do nosso lado. Estamos trabalhando para consertar isso o mais rápido possível.
    <br><br>
    Por favor, tente novamente mais tarde ou contate o suporte se o problema persistir.
@endsection

@section('actions')
    <a href="{{ url('/') }}"
        class="inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:text-lg">
        Voltar para a Página Inicial
    </a>
@endsection