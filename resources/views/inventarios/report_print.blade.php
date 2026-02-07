@extends('layouts.app')

@section('title', 'Impressão de Inventário')

@section('content')
    <div class="bg-white p-8 max-w-5xl mx-auto shadow-none print:shadow-none print:p-0">
        <!-- Institutional Header -->
        <div class="text-center border-b-2 border-black pb-4 mb-6">
            <h1 class="text-2xl font-black uppercase tracking-widest">Congregação Cristã no Brasil</h1>
            <h2 class="text-lg font-bold uppercase">{{ $inventario->igreja_nome }}</h2>
            <p class="text-sm font-mono mt-2">RELATÓRIO DE INVENTÁRIO FÍSICO - FORMULÁRIO FOR.AI.22</p>
        </div>

        <!-- Inventory Meta -->
        <div class="grid grid-cols-3 gap-4 mb-8 text-xs">
            <div class="border p-2">
                <p class="font-bold opacity-50 uppercase">Referência</p>
                <p class="text-sm font-black">{{ $inventario->mes }}/{{ $inventario->ano }}</p>
            </div>
            <div class="border p-2">
                <p class="font-bold opacity-50 uppercase">Código Inventário</p>
                <p class="text-sm font-black">{{ $inventario->codigo_unico }}</p>
            </div>
            <div class="border p-2">
                <p class="font-bold opacity-50 uppercase">Data de Emissão</p>
                <p class="text-sm font-black">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="mb-8">
            <h3 class="bg-gray-100 p-1 px-3 text-xs font-black uppercase border border-b-0">Resumo da Conferência</h3>
            <table class="w-full text-xs border">
                <tr>
                    <td class="p-2 border font-bold">Total de Bens Cadastrados</td>
                    <td class="p-2 border text-right">{{ $inventario->detalhes()->count() }}</td>
                    <td class="p-2 border font-bold">Bens Localizados</td>
                    <td class="p-2 border text-right">
                        {{ $inventario->detalhes()->where('status_leitura', 'encontrado')->count() }}
                    </td>
                </tr>
                <tr>
                    <td class="p-2 border font-bold">Bens Não Localizados (Faltas)</td>
                    <td class="p-2 border text-right text-red-600">
                        {{ $inventario->detalhes()->where('status_leitura', 'nao_encontrado')->count() }}
                    </td>
                    <td class="p-2 border font-bold">Novos Bens Detectados</td>
                    <td class="p-2 border text-right">
                        {{ \App\Models\Divergencia::where('inventario_id', $inventario->id)->where('codigo_divergencia', '02')->count() }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Divergences Section -->
        <div class="mb-12">
            <h3 class="bg-gray-100 p-1 px-3 text-xs font-black uppercase border border-b-0">Ocorrências e Divergências
                (FOR.AI.01)</h3>
            <table class="w-full text-xs border">
                <thead class="bg-gray-50 font-bold">
                    <tr>
                        <td class="p-1 px-2 border w-16">Cód</td>
                        <td class="p-1 px-2 border w-24">Etiqueta</td>
                        <td class="p-1 px-2 border">Descrição / Bem Móvel</td>
                        <td class="p-1 px-2 border w-32">Localização</td>
                    </tr>
                </thead>
                <tbody>
                    @php $divergencias = \App\Models\Divergencia::where('inventario_id', $inventario->id)->get(); @endphp
                    @forelse($divergencias as $div)
                        <tr>
                            <td class="p-1 px-2 border text-center font-bold">{{ $div->codigo_divergencia }}</td>
                            <td class="p-1 px-2 border font-mono">{{ $div->id_bem }}</td>
                            <td class="p-1 px-2 border italic">{{ $div->descricao }}</td>
                            <td class="p-1 px-2 border">{{ $div->id_dependencia_nova }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-400 italic">Nenhuma ocorrência registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signature Block (Screenshot Requirement) -->
        <div class="mt-20 grid grid-cols-2 gap-12 text-center text-xs">
            <div class="flex flex-col items-center">
                <div class="w-64 border-t border-black mb-2"></div>
                <p class="font-black uppercase text-[10px] mb-1">Responsáveis (Assinaturas)</p>
                <div class="text-gray-700 italic text-[11px] leading-tight">
                    {!! nl2br(e($inventario->responsavel)) !!}
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-64 border-t border-black mb-2"></div>
                <p class="font-black uppercase text-[10px] mb-1">Inventariantes (Equipe)</p>
                <div class="text-gray-700 italic text-[11px] leading-tight">
                    {!! nl2br(e($inventario->inventariante)) !!}
                </div>
            </div>
        </div>

        <!-- Print Footer -->
        <div class="mt-12 text-[9px] text-gray-400 font-mono text-center border-t pt-2">
            SIBEM - Sistema Informatizado de Bens Móveis | Hash: {{ md5($inventario->codigo_unico) }} | ID:
            {{ $inventario->id }}
        </div>

        <!-- Print Button (Hidden on Print) -->
        <div class="mt-12 flex justify-center print:hidden">
            <button onclick="window.print()"
                class="bg-black text-white px-8 py-3 rounded font-black tracking-widest hover:bg-gray-800 transition shadow-xl">
                🖨️ IMPRIMIR RELATÓRIO OFICIAL
            </button>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 1.5cm;
            }
        }
    </style>
@endsection