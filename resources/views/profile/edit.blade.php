@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
    <div class="max-w-2xl mx-auto animate-fadeIn">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Meu Perfil</h1>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-100 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nome -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome', $user->nome) }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <hr class="border-gray-100">

                <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-600 mb-4">
                    <p><strong>Perfil:</strong> {{ $user->perfil->nome ?? 'N/A' }}</p>
                    <p><strong>Regional:</strong> {{ $user->regional->nome ?? 'Global' }}</p>
                    <p><strong>Locais:</strong>
                        @if($user->locais->count() > 0)
                            {{ $user->locais->pluck('nome')->join(', ') }}
                        @else
                            {{ $user->local->nome ?? 'Nenhum' }}
                        @endif
                    </p>
                </div>

                <!-- Senha -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nova Senha (Opcional)</label>
                        <input type="password" name="password"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Confirmar Nova Senha</label>
                        <input type="password" name="password_confirmation"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                        SALVAR ALTERAÇÕES
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection