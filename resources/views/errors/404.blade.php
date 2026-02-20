@extends('layouts.error')

@section('title', 'Página Não Encontrada')

@section('code', '404')

@section('message', 'Página Não Encontrada')

@section('description')
    O recurso que você está procurando pode ter sido removido, ter seu nome alterado ou estar temporariamente indisponível.
@endsection

@section('actions')
    <a href="{{ url('/') }}"
        class="inline-block px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:text-lg">
        Voltar para a Página Inicial
    </a>
@endsection