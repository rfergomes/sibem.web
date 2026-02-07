<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alterar Senha - SIBEM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-fadeIn">
        <div class="px-8 pt-8 pb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Alterar Senha</h2>
            <p class="text-sm text-gray-500 mt-2">
                {{ __('Por questões de segurança, você deve alterar sua senha antes de continuar.') }}
            </p>
        </div>

        <div class="px-8 pb-8">
            <form method="POST" action="{{ route('password.update_changed') }}" class="space-y-6">
                @csrf

                <!-- Senha Atual -->
                <div>
                    <label for="current_password" class="block text-sm font-bold text-gray-700 mb-1">Senha Atual</label>
                    <input id="current_password" type="password" name="current_password" required
                        autocomplete="current-password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all" />
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nova Senha -->
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Nova Senha</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all" />
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirmar Nova Senha -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">Confirmar Nova
                        Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all" />
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98] flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        {{ __('Alterar Senha e Entrar') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-xs text-gray-400 hover:text-gray-600 font-medium transition-colors">
                        Cancelar e Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>