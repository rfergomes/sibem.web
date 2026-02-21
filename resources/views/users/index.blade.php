@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
    <div class="animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Gestão de Usuários</h1>
                <p class="text-xs text-gray-500">Controle de acesso e permissões.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <x-view-toggle storage-key="users_view_mode" />
                <a href="{{ route('users.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-lg shadow-blue-500/30 transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Usuário
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-100 font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-100 font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- TABLE VIEW --}}
        <div data-view-content="table" class="hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Nome / Email</th>
                                <th class="px-6 py-4">Perfil</th>
                                <th class="px-6 py-4">Escopo</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                                                    <span class="text-sm font-bold text-white">{{ $user->initials }}</span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $user->nome }}</div>
                                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $user->perfil->nome ?? 'Sem Perfil' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if($user->regional)
                                            <div class="font-semibold text-gray-700">{{ $user->regional->nome }}</div>
                                        @endif

                                        @if($user->locais && $user->locais->count() > 0)
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                {{ $user->locais->pluck('nome')->join(', ') }}
                                            </div>
                                        @elseif($user->local)
                                            {{-- Fallback for legacy local_id --}}
                                            <div class="text-xs text-gray-500">{{ $user->local->nome }}</div>
                                        @endif

                                        @if(!$user->regional && $user->locais->isEmpty() && !$user->local)
                                            <span class="text-gray-400 italic">Global</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->active)
                                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-1"></span> Ativo
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block mr-1"></span> Inativo
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-3">
                                        @can('update', $user)
                                            <a href="{{ route('users.edit', $user) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-lg transition-colors"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endcan

                                        @if(auth()->id() !== $user->id)
                                            @can('delete', $user)
                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                                        title="Excluir">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD VIEW --}}
        <div data-view-content="card" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($users as $user)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all">
                        <!-- Avatar Section -->
                        <div class="p-4">
                            <div class="flex items-center gap-3 mb-3">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-lg font-bold text-white">{{ $user->initials }}</span>
                                    </div>
                                @endif
                                
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 text-sm truncate" title="{{ $user->nome }}">{{ $user->nome }}</h3>
                                    <p class="text-xs text-gray-500 truncate" title="{{ $user->email }}">{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <!-- Perfil Badge -->
                            <div class="mb-3">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $user->perfil->nome ?? 'Sem Perfil' }}
                                </span>
                            </div>
                            
                            <!-- Escopo Info -->
                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-2 mb-3">
                                <p class="text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $user->regional->nome ?? 'Global' }}
                                </p>
                                @if($user->locais && $user->locais->count() > 0)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $user->locais->count() }} {{ $user->locais->count() == 1 ? 'administração' : 'administrações' }}
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Status -->
                            <div class="flex items-center justify-between text-xs">
                                <span class="{{ $user->active ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    ● {{ $user->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex gap-2">
                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="flex-1 text-center py-1.5 rounded bg-white border border-gray-200 hover:border-blue-300 hover:text-blue-600 text-gray-600 text-xs font-semibold transition-all">
                                    Editar
                                </a>
                            @endcan
                            
                            @if(auth()->id() !== $user->id)
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full py-1.5 rounded bg-white border border-gray-200 hover:border-red-300 hover:text-red-600 text-gray-600 text-xs font-semibold transition-all">
                                            Excluir
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection