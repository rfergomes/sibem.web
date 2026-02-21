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
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                <!-- Nome -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Nome Completo</label>
                    <input type="text" name="nome" value="{{ old('nome', $user->nome ?? '') }}" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                    @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Avatar -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Avatar</label>
                    
                    @if(isset($user) && $user->avatar_url)
                        <div class="mb-3 flex items-center gap-4">
                            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 mb-2">Avatar atual</p>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded text-red-600 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-red-600 font-semibold">Remover avatar</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    
                    <input type="file" name="avatar" accept="image/*" 
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:font-semibold">
                    <p class="text-xs text-gray-500 mt-1">{{ isset($user) && $user->avatar ? 'Envie uma nova imagem para substituir' : 'Formatos aceitos: JPG, PNG. Tamanho máximo: 2MB' }}</p>
                    @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Senha -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Senha
                            {{ isset($user) ? '(Deixe em branco para manter)' : '' }}</label>
                        <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" {{ isset($user) ? '' : 'required' }}
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Perfil -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Perfil de Acesso</label>
                    <select name="perfil_id" required
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                        <option value="">Selecione...</option>
                        @foreach($perfis as $perfil)
                            <option value="{{ $perfil->id }}" {{ old('perfil_id', $user->perfil_id ?? '') == $perfil->id ? 'selected' : '' }}>
                                {{ $perfil->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('perfil_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Status Ativo -->
                <div class="flex items-center gap-4 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="active" value="1" 
                            {{ old('active', $user->active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 focus:ring-green-500/20">
                        <span class="text-sm font-medium text-gray-700">Usuário Ativo</span>
                    </label>
                    <p class="text-xs text-gray-500">Desmarque para desativar o acesso do usuário</p>
                </div>

                <!-- Escopo Manual (Simples por enquanto) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Regional (Opcional)</label>
                        <select name="regional_id" id="regional_id"
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                            <option value="">Nenhuma (Global)</option>
                            @foreach($regionais as $regional)
                                <option value="{{ $regional->id }}" {{ old('regional_id', $user->regional_id ?? '') == $regional->id ? 'selected' : '' }}>
                                    {{ $regional->nome }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ao selecionar uma regional, a lista abaixo será filtrada.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">Localidade (Múltipla Escolha)</label>

                        <!-- Search Input -->
                        <div class="mb-2 relative">
                            <input type="text" id="search_locais" placeholder="Filtrar localidades..."
                                class="w-full pl-8 pr-4 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <div class="h-64 overflow-y-auto border border-gray-300 rounded-lg p-2 bg-white" id="locais_list">
                            @foreach($locais as $local)
                                <div class="flex items-center gap-2 mb-1 local-item"
                                    data-regional-id="{{ $local->regional_id }}" data-nome="{{ strtolower($local->nome) }}">
                                    <input type="checkbox" name="locais[]" value="{{ $local->id }}" id="local_{{ $local->id }}"
                                        {{ isset($user) && $user->locais->contains($local->id) ? 'checked' : '' }}
                                        class="rounded text-blue-600 focus:ring-blue-500">
                                    <label for="local_{{ $local->id }}"
                                        class="text-sm text-gray-700 cursor-pointer select-none">
                                        {{ $local->nome }}
                                        <span class="text-xs text-gray-400">({{ $local->regional->nome ?? 'N/A' }})</span>
                                    </label>
                                </div>
                            @endforeach
                            <div id="no_results" class="hidden text-sm text-gray-500 text-center py-2">Nenhuma localidade
                                encontrada.</div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <span id="count_visible">0</span> visíveis de {{ count($locais) }} total.
                        </p>
                    </div>
                </div>

                <!-- Administração Padrão ao Logar -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wide">
                        🏠 Administração Padrão (ao Logar)
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Se o usuário tiver acesso a várias administrações, defina qual será carregada automaticamente no login.</p>
                    <select name="default_local_id"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm px-4 py-3 font-medium transition-all duration-300">
                        <option value="">-- Nenhuma (carregar automaticamente a primeira disponível) --</option>
                        @foreach($locais as $local)
                            <option value="{{ $local->id }}"
                                {{ old('default_local_id', $user->default_local_id ?? '') == $local->id ? 'selected' : '' }}>
                                {{ $local->nome }}
                                @if($local->regional)
                                    ({{ $local->regional->nome }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('default_local_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const searchInput = document.getElementById('search_locais');
                        const regionalSelect = document.getElementById('regional_id');
                        const localItems = document.querySelectorAll('.local-item');
                        const countVisibleSpan = document.getElementById('count_visible');
                        const noResultsMsg = document.getElementById('no_results');

                        function filterLocais() {
                            const searchTerm = searchInput.value.toLowerCase();
                            const selectedRegional = regionalSelect.value;
                            let visibleCount = 0;

                            localItems.forEach(item => {
                                const nome = item.getAttribute('data-nome');
                                const regionalId = item.getAttribute('data-regional-id');

                                const matchesSearch = nome.includes(searchTerm);
                                const matchesRegional = selectedRegional === '' || regionalId === selectedRegional;

                                if (matchesSearch && matchesRegional) {
                                    item.style.display = 'flex';
                                    visibleCount++;
                                } else {
                                    item.style.display = 'none';
                                    // Optional: Uncheck hidden items? Usually no, keep selection.
                                }
                            });

                            countVisibleSpan.textContent = visibleCount;
                            noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
                        }

                        searchInput.addEventListener('input', filterLocais);
                        regionalSelect.addEventListener('change', filterLocais);

                        // Initial filter
                        filterLocais();
                    });
                </script>
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