@extends('layouts.app')

@section('title', 'Administrações')

@section('content')
    <div class="animate-fadeIn">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Administrações (Locais)</h1>
                <p class="text-sm text-gray-500">Gerencie as unidades administrativas e seus bancos de dados.</p>
            </div>
            <a href="{{ route('locais.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98] flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nova Administração
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-100 font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-6 p-4 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-100 font-medium">
                {{ session('warning') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($locais as $local)
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden group">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold {{ $local->active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-50 text-gray-500 border border-gray-100' }}">
                                {{ $local->active ? 'ATIVO' : 'INATIVO' }}
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $local->nome }}</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-4">
                            {{ $local->regional->nome ?? 'Regional não definida' }}
                            {{ optional($local->regional)->uf ? '- ' . $local->regional->uf : '' }}
                        </p>

                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 7v10l8 4m0-10L4 7m8 4v10M12 4v10m0-10l8 4m-8-4L4 7m8 4l8-4m0 10l-8 4" />
                                </svg>
                                <span>DB: <span class="font-medium text-gray-900">{{ $local->db_name }}</span></span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                </svg>
                                <span>Host: <span class="font-medium text-gray-900">{{ $local->db_host }}</span></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('locais.edit', $local->id) }}"
                                class="flex-1 text-center py-2 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-colors">Editar</a>

                            <form id="provision-form-{{ $local->id }}" action="{{ route('locais.provision', $local->id) }}"
                                method="POST" class="flex-1">
                                @csrf
                                <button type="button"
                                    onclick="confirmAction('Inicializar Banco?', 'Isso irá rodar as migrações e dados padrão no banco desta administração.', () => document.getElementById('provision-form-{{ $local->id }}').submit())"
                                    class="w-full py-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition-colors">
                                    Provisionar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $locais->links() }}
        </div>
    </div>
@endsection