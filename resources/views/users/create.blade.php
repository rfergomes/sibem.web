@extends('layouts.app')

@section('title', isset($user) ? 'Editar Usuário' : 'Novo Usuário')

@section('content')
    <div class="max-w-2xl mx-auto animate-fadeIn">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($user) ? 'Editar Usuário' : 'Novo Usuário' }}</h1>
            <a href="{{ route('users.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900">Voltar para
                Lista</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST"
                class="space-y-6">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                <!-- Nome -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome', $user->nome ?? '') }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Senha (com Alpine.js para toggle) -->
                <div x-data="{ autoGenerate: true }" class="space-y-6">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="auto_generate_password" id="auto_generate_password"
                            x-model="autoGenerate" value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="auto_generate_password" class="text-sm font-medium text-gray-700">
                            Gerar senha automaticamente e enviar por e-mail
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-show="!autoGenerate" x-transition>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Senha
                                {{ isset($user) ? '(Deixe em branco para manter)' : '' }}</label>
                            <input type="password" name="password" :required="!autoGenerate && '{{ !isset($user) }}'"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Confirmar Senha</label>
                            <input type="password" name="password_confirmation"
                                :required="!autoGenerate && '{{ !isset($user) }}'"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Perfil -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Perfil de Acesso</label>
                    <select name="perfil_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        <option value="">Selecione...</option>
                        @foreach($perfis as $perfil)
                            <option value="{{ $perfil->id }}" {{ old('perfil_id', $user->perfil_id ?? '') == $perfil->id ? 'selected' : '' }}>
                                {{ $perfil->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('perfil_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Escopo Manual (Simples por enquanto) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Regional (Opcional)</label>
                        <select name="regional_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            <option value="">Nenhuma (Global)</option>
                            @foreach($regionais as $regional)
                                <option value="{{ $regional->id }}" {{ old('regional_id', $user->regional_id ?? '') == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Localidade (Múltipla Escolha)</label>
                        <div class="h-32 overflow-y-auto border border-gray-300 rounded-lg p-2 bg-white">
                            @foreach($locais as $local)
                                <div class="flex items-center gap-2 mb-1">
                                    <input type="checkbox" name="locais[]" value="{{ $local->id }}" id="local_{{ $local->id }}"
                                        {{ isset($user) && $user->locais->contains($local->id) ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500">
                                    <label for="local_{{ $local->id }}"
                                        class="text-sm text-gray-700 cursor-pointer select-none">
                                        {{ $local->nome }} <span
                                            class="text-xs text-gray-400">({{ $local->regional->nome ?? 'N/A' }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Selecione uma ou mais.</p>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                        {{ isset($user) ? 'SALVAR ALTERAÇÕES' : 'CRIAR USUÁRIO' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection