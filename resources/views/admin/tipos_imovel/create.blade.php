@extends('layouts.app')

@section('title', 'Novo Tipo de Imóvel')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn max-w-2xl mx-auto">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Novo Tipo de Imóvel</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tipos-imovel.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="nome" class="block text-xs font-bold text-gray-500 uppercase mb-2">Nome do Tipo</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome') }}"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                    required placeholder="Ex: TEMPLO, ANEXO, BARRACÃO...">
                <p class="text-xs text-gray-500 mt-1">Use letras maiúsculas para manter o padrão.</p>
            </div>

            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('tipos-imovel.index') }}"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-gray-500 hover:bg-gray-600 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    Salvar
                </button>
            </div>
        </form>
    </div>
@endsection
