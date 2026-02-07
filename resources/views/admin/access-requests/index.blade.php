@extends('layouts.app')

@section('title', 'Solicitações de Acesso - SIBEM')

@section('content')
    <div class="animate-fadeIn">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Solicitações de Acesso</h1>
                <p class="text-sm text-gray-500">Gerencie os pedidos de cadastro no sistema.</p>
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

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($solicitacoes->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <p class="text-lg font-medium">Nenhuma solicitação pendente.</p>
                    <p class="text-sm">Novas solicitações aparecerão aqui.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Data</th>
                                <th class="px-6 py-4">Solicitante</th>
                                <th class="px-6 py-4">Contato</th>
                                <th class="px-6 py-4">Localidade</th>
                                <th class="px-6 py-4">Observações</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($solicitacoes as $solicitacao)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-xs">
                                        {{ $solicitacao->created_at->format('d/m/Y H:i') }}
                                        <div class="text-gray-400">{{ $solicitacao->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $solicitacao->nome }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-800">{{ $solicitacao->email }}</div>
                                        @if($solicitacao->telefone)
                                            <div class="text-xs text-gray-500">{{ $solicitacao->telefone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800">{{ $solicitacao->cidade }}</div>
                                        @if($solicitacao->regional)
                                            <span
                                                class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $solicitacao->regional->nome }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-500 max-w-xs break-words">
                                            {{ $solicitacao->observacoes ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        <form action="{{ route('admin.access-requests.reject', $solicitacao->id) }}" method="POST"
                                            onsubmit="return confirm('Rejeitar solicitação?');">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-all">Rejeitar</button>
                                        </form>

                                        <form action="{{ route('admin.access-requests.approve', $solicitacao->id) }}" method="POST"
                                            onsubmit="return confirm('Aprovar e criar usuário?');">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition-all">Aprovar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection