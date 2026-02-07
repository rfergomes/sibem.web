<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Senha - SIBEM CCB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-gray-900 bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-scaleIn m-4">
        <div class="p-8">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.543 22H3v-3m14-14a2 2 0 10-4 0 4 4 0 004 0z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Recuperar Senha</h2>
                <p class="text-gray-500 text-sm mt-2">Informe seu e-mail para receber o link de redefinição.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 bg-green-50 text-green-700 p-3 rounded-lg text-sm font-medium border border-green-100">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none transition-all"
                        placeholder="seu@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                    ENVIAR LINK
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}"
                    class="text-sm font-bold text-gray-400 hover:text-blue-600 transition-colors">
                    ← Voltar para o Login
                </a>
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