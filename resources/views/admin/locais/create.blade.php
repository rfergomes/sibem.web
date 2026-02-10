@extends('layouts.app')

@section('title', 'Nova Administração - SIBEM')

@section('content')
    <div class="animate-fadeIn max-w-4xl">
        <div class="mb-8">
            <a href="{{ route('locais.index') }}"
                class="text-sm font-bold text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para lista
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Nova Administração</h1>
            <p class="text-sm text-gray-500">Configure uma nova unidade administrativa e sua conexão de banco de dados.</p>
        </div>

        <form action="{{ route('locais.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Informações Gerais
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nome" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome da
                            Administração</label>
                        <input type="text" name="nome" id="nome" required value="{{ old('nome') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm"
                            placeholder="Ex: Administração Campinas">
                    </div>

                    <div>
                        <label for="regional_id" class="block text-xs font-bold text-gray-700 uppercase mb-1">Regional
                            Vinculada</label>
                        <select name="regional_id" id="regional_id" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm bg-white">
                            <option value="">Selecione a Regional...</option>
                            @foreach($regionais as $regional)
                                <option value="{{ $regional->id }}" {{ old('regional_id') == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nome }} {{ $regional->uf ? '(' . $regional->uf . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-4 pt-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="active" value="1" checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500/20">
                            <span class="text-sm font-medium text-gray-700">Administração Ativa</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10l8 4m0-10L4 7m8 4v10M12 4v10m0-10l8 4m-8-4L4 7m8 4l8-4m0 10l-8 4" />
                        </svg>
                        Conexão do Banco de Dados (Tenant)
                    </h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="db_host" class="block text-xs font-bold text-gray-700 uppercase mb-1">Host do
                            Banco</label>
                        <input type="text" name="db_host" id="db_host" required value="{{ old('db_host', 'localhost') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                    </div>
                    <div>
                        <label for="db_name" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome do Banco de
                            Dados</label>
                        <input type="text" name="db_name" id="db_name" required value="{{ old('db_name') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm"
                            placeholder="Ex: sibemo33_cps">
                    </div>
                    <div>
                        <label for="db_user" class="block text-xs font-bold text-gray-700 uppercase mb-1">Usuário do
                            Banco</label>
                        <input type="text" name="db_user" id="db_user" required value="{{ old('db_user') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                    </div>
                    <div>
                        <label for="db_password" class="block text-xs font-bold text-gray-700 uppercase mb-1">Senha do
                            Banco</label>
                        <input type="password" name="db_password" id="db_password" value="{{ old('db_password') }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="bg-blue-600 p-2 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-blue-900 mb-1">Provisionamento Automático</h3>
                        <p class="text-sm text-blue-700 mb-4">Deseja que o sistema tente criar o banco de dados e
                            inicializar as tabelas automaticamente agora?</p>
                        <label
                            class="flex items-center gap-2 cursor-pointer inline-flex bg-white px-4 py-2 rounded-lg border border-blue-200 shadow-sm">
                            <input type="checkbox" name="provision" value="1" checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500/20">
                            <span class="text-sm font-bold text-blue-700">Sim, inicializar banco de dados após salvar</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-4">
                <a href="{{ route('locais.index') }}"
                    class="px-6 py-2.5 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition-all">Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-8 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                    SALVAR ADMINISTRAÇÃO
                </button>
            </div>
        </form>
    </div>
@endsection