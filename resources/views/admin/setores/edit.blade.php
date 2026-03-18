@extends('layouts.app')

@section('title', 'Editar Setor')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Editar Setor</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('setores.update', $setor->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nome" class="block text-xs font-bold text-gray-500 uppercase mb-2">Nome do Setor</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome', $setor->nome) }}"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                    required>
            </div>

            <div class="mb-4">
                <label for="active" class="inline-flex items-center">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" id="active" value="1"
                        class="rounded border-gray-300 text-ccb-blue-600 shadow-sm focus:border-ccb-blue-300 focus:ring focus:ring-ccb-blue-200 focus:ring-opacity-50"
                        {{ $setor->active ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-600">Ativo</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('setores.index') }}"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-gray-500 hover:bg-gray-600 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-95">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                    Atualizar
                </button>
            </div>
        </form>
    </div>
@endsection
