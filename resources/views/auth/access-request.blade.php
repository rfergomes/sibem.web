<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitar Acesso - SIBEM CCB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased text-gray-900 bg-gray-100 flex items-center justify-center py-12">

    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden animate-scaleIn m-4">
        <div class="bg-[#111827] px-8 py-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/30 to-transparent"></div>
            <div class="relative z-10 flex flex-col items-center">
                <div
                    class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg mb-3">
                    A
                </div>
                <h2 class="text-xl font-bold text-white tracking-tight">Solicitação de Acesso</h2>
                <p class="text-blue-200 text-xs font-medium uppercase tracking-widest mt-1">SIBEM - Sistema de
                    Inventário</p>
            </div>
        </div>

        <div class="p-8">

            @if(session('success'))
                <div
                    class="bg-green-50 text-green-700 p-4 rounded-lg text-sm mb-6 border border-green-100 flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <div>
                        <span class="font-bold block">Sucesso!</span>
                        {{ session('success') }}
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Voltar para o Login</a>
                </div>
            @else
                <p class="text-gray-500 text-sm mb-6">Preencha o formulário abaixo para solicitar acesso ao sistema. Seus
                    dados serão enviados para análise da administração.</p>

                @if($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg text-sm mb-6 border border-red-100">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('access-request.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nome" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome
                                Completo</label>
                            <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-700 uppercase mb-1">E-mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telefone" class="block text-xs font-bold text-gray-700 uppercase mb-1">Telefone /
                                Celular</label>
                            <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                        </div>
                        <div>
                            <label for="cidade" class="block text-xs font-bold text-gray-700 uppercase mb-1">Cidade /
                                Comum</label>
                            <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="regional_id" class="block text-xs font-bold text-gray-700 uppercase mb-1">Regional
                            (Opcional)</label>
                        <select name="regional_id" id="regional_id"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all text-sm bg-white">
                            <option value="">Selecione uma Regional...</option>
                            @foreach($regionais as $regional)
                                <option value="{{ $regional->id }}" {{ old('regional_id') == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="observacoes" class="block text-xs font-bold text-gray-700 uppercase mb-1">Observações /
                            Motivo</label>
                        <textarea name="observacoes" id="observacoes" rows="3"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder-gray-400 text-sm">{{ old('observacoes') }}</textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-between">
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-gray-500 hover:text-gray-700">Cancelar</a>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98]">
                            Enviar Solicitação
                        </button>
                    </div>
                </form>
            @endif
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