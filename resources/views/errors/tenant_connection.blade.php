@extends('layouts.error')

@section('title', 'Erro de Conexão')

@section('code', 'Sem Conexão')

@section('message', 'Falha ao Conectar à Administração')

@section('description')
    O sistema não conseguiu estabelecer uma conexão com o banco de dados da administração selecionada.
    Isso pode ocorrer devido a credenciais inválidas ou manutenção no servidor.
@endsection

@section('actions')
    <div class="space-y-3">
        <a href="{{ route('dashboard') }}"
            class="block w-full text-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Tentar Novamente
        </a>
        <a href="mailto:suporte@sibem.ccb.org.br"
            class="block w-full text-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Contatar Suporte
        </a>

        <form action="{{ route('logout') }}" method="POST" class="block w-full">
            @csrf
            <button type="submit"
                class="w-full text-center py-2 px-4 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Sair / Logout
            </button>
        </form>
    </div>
@endsection