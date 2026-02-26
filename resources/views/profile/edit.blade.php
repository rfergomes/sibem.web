@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
    <div class="max-w-4xl mx-auto animate-fadeIn py-8 px-4">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Meu Perfil</h1>
            <p class="text-gray-500 mt-1">Gerencie suas informações pessoais e configurações de conta</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-100 font-medium flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Avatar Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Foto de Perfil</h2>
                
                <div class="flex flex-col md:flex-row items-center gap-6">
                    {{-- Avatar Preview --}}
                    <div class="relative group">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" id="avatar-preview" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-lg">
                        @else
                            <div id="avatar-preview" class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center border-4 border-gray-100 shadow-lg">
                                <span class="text-4xl font-bold text-white">{{ $user->initials }}</span>
                            </div>
                        @endif
                        
                        {{-- Upload Overlay --}}
                        <label for="avatar-input" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </label>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(event)">
                    </div>
                    
                    {{-- Avatar Actions --}}
                    <div class="flex-1 text-center md:text-left">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $user->nome }}</h3>
                        <p class="text-sm text-gray-500 mb-4">JPG, PNG ou GIF. Tamanho máximo de 2MB</p>
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label for="avatar-input" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Enviar Nova Foto
                            </label>
                            
                            @if($user->avatar)
                                <label class="inline-flex items-center justify-center px-4 py-2 bg-white hover:bg-red-50 text-red-600 text-sm font-semibold rounded-lg border border-red-200 transition-colors cursor-pointer">
                                    <input type="checkbox" name="remove_avatar" value="1" class="mr-2 rounded text-red-600">
                                    Remover Foto
                                </label>
                            @endif
                        </div>
                        @error('avatar') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Personal Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Informações Pessoais</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nome --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nome Completo</label>
                        <input type="text" name="nome" value="{{ old('nome', $user->nome) }}" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                        @error('nome') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400">
                        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Account Details (Read-only) --}}
            <div class="bg-gradient-to-br from-blue-50 to-ccb-blue-50 rounded-xl border border-blue-100 p-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Detalhes da Conta</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Perfil de Acesso</label>
                        <div class="px-4 py-3 bg-white rounded-lg border border-blue-200">
                            <span class="font-semibold text-gray-900">{{ $user->perfil->nome ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Regional</label>
                        <div class="px-4 py-3 bg-white rounded-lg border border-blue-200">
                            <span class="font-semibold text-gray-900">{{ $user->regional->nome ?? 'Global' }}</span>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Administrações</label>
                        <div class="px-4 py-3 bg-white rounded-lg border border-blue-200">
                            <span class="font-semibold text-gray-900">
                                @if($user->locais->count() > 0)
                                    {{ $user->locais->pluck('nome')->join(', ') }}
                                @else
                                    {{ $user->local->nome ?? 'Nenhuma' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex items-start gap-2 text-sm text-blue-700">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>Essas informações são gerenciadas pelo administrador do sistema e não podem ser alteradas aqui.</p>
                </div>
            </div>

            {{-- Security --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Segurança</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nova Senha (Opcional)</label>
                        <input type="password" name="password"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                            placeholder="••••••••">
                        @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Confirmar Nova Senha</label>
                        <input type="password" name="password_confirmation"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300 placeholder-gray-400"
                            placeholder="••••••••">
                    </div>
                </div>
                
                <div class="mt-4 flex items-start gap-2 text-sm text-gray-500">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <p>Deixe em branco para manter a senha atual. Use no mínimo 8 caracteres.</p>
                </div>
            </div>

            {{-- Notification Preferences --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900">Preferências de Notificação</h2>
                </div>

                <div class="mb-6 flex items-center justify-between p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                    <div class="text-sm text-blue-700">
                        <p class="font-bold">Dica de Teste</p>
                        <p>Use o botão ao lado para verificar se as configurações estão funcionando.</p>
                    </div>
                    <button type="button" @click="$dispatch('test-notification')" class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        Testar Agora
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Access Requests (Admins only or relevant) --}}
                    @if(in_array($user->perfil->slug ?? '', ['admin', 'administrador', 'admin_sistema', 'admin_regional']))
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-100 bg-gray-50/30">
                        <div>
                            <p class="font-semibold text-gray-900">Novas Solicitações de Acesso</p>
                            <p class="text-xs text-gray-500">Receba alertas quando novos usuários pedirem acesso ao sistema</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notification_settings[access_request]" value="0">
                            <input type="checkbox" name="notification_settings[access_request]" value="1" class="sr-only peer" {{ ($user->notification_settings['access_request'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    @endif

                    {{-- Inventory Alerts --}}
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-100 bg-gray-50/30">
                        <div>
                            <p class="font-semibold text-gray-900">Inventários em Aberto</p>
                            <p class="text-xs text-gray-500">Alertas automáticos para inventários parados há mais de 7 dias</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notification_settings[inventory_open]" value="0">
                            <input type="checkbox" name="notification_settings[inventory_open]" value="1" class="sr-only peer" {{ ($user->notification_settings['inventory_open'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    {{-- Access Status --}}
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-100 bg-gray-50/30">
                        <div>
                            <p class="font-semibold text-gray-900">Status de Solicitação</p>
                            <p class="text-xs text-gray-500">Receber notificações sobre aprovação ou rejeição de contas</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notification_settings[access_request_status]" value="0">
                            <input type="checkbox" name="notification_settings[access_request_status]" value="1" class="sr-only peer" {{ ($user->notification_settings['access_request_status'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    {{-- WebPush Toggle --}}
                    <div class="flex items-center justify-between p-4 rounded-lg border border-blue-50 bg-blue-50/30">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Notificações no Navegador (Push)</p>
                                <p class="text-xs text-blue-600">Receba alertas mesmo com o SIBEM fechado. Requer ativação no navegador.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notification_settings[browser_push]" value="0">
                            <input type="checkbox" name="notification_settings[browser_push]" value="1" id="push-toggle" class="sr-only peer" {{ ($user->notification_settings['browser_push'] ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <hr class="border-gray-100 my-4">

                    {{-- Sound Toggle --}}
                    <div class="flex items-center justify-between p-4 rounded-lg border border-gray-100 bg-gray-50/30">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Sons de Alerta</p>
                                <p class="text-xs text-gray-500">Tocar um som breve ao receber novas notificações</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notification_settings[sound_enabled]" value="0">
                            <input type="checkbox" name="notification_settings[sound_enabled]" value="1" class="sr-only peer" {{ ($user->notification_settings['sound_enabled'] ?? true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ url()->previous() }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all active:scale-95 text-center">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    {{-- Avatar Preview Script --}}
    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    preview.innerHTML = '';
                    preview.className = 'w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-lg';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-full rounded-full object-cover';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
