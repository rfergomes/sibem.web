<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIBEM CCB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-gray-900 bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-scaleIn m-4">
        <div class="bg-[#111827] px-8 py-10 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/30 to-transparent"></div>
            <div class="relative z-10 flex flex-col items-center">
                <div
                    class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4">
                    S
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">SIBEM</h2>
                <p class="text-blue-200 text-xs font-medium uppercase tracking-widest mt-1">Sistema de Inventário de
                    Bens Móveis</p>
            </div>
        </div>

        <div class="p-8">
            <h3 class="text-lg font-bold text-gray-800 text-center mb-6">Acesso ao Sistema</h3>

            @if($errors->any())
                <div
                    class="bg-red-50 text-red-600 p-4 rounded-lg text-sm mb-6 border border-red-100 flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <span class="font-bold block">Erro no Login</span>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-gray-700 uppercase mb-1">E-mail ou
                        Usuário</label>
                    <input type="email" name="email" id="email" required autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm font-medium"
                        placeholder="seu.email@sibem.ccb.org.br">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-sm font-bold text-gray-700">Senha</label>
                        <a href="{{ route('password.request') }}"
                            class="text-xs font-bold text-blue-600 hover:text-blue-800">Esqueceu sua senha?</a>
                    </div>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500/20">
                        <span class="text-xs font-medium text-gray-600">Lembrar-me</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                    ENTRAR
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">Não possui acesso? <a href="{{ route('access-request.create') }}"
                        class="text-blue-600 font-bold hover:underline">Solicitar cadastro</a></p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
            <p class="text-[10px] text-gray-400 font-medium">© {{ date('Y') }} SIBEM CCB. Todos os direitos reservados.
            </p>
        </div>
    </div>

    <style>
        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-scaleIn {
            animation: scaleIn 0.3s ease-out;
        }
    </style>
</body>

</html>