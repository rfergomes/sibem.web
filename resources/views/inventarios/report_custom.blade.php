@extends('layouts.app')

@section('title', 'Relatório Geral de Inventário')

@section('content')
    <div class="bg-white p-8 max-w-5xl mx-auto shadow-none print:shadow-none print:p-0 font-sans">
        <!-- Header (Screenshots 2 & 3 Style) -->
        <div class="flex justify-between items-start border-b-2 border-black pb-2 mb-4">
            <div class="flex items-center gap-4">
                <div class="border-2 border-black p-2 font-black text-center text-xs leading-tight w-40">
                    CONGREGAÇÃO CRISTÃ<br>NO<br>BRASIL
                </div>
                <div>
                    <h1 class="text-xl font-black uppercase text-blue-900 tracking-tighter">Inventário Geral de Bens Móveis
                        - {{ $inventario->ano }}</h1>
                    <div class="text-[9px] font-bold text-gray-600 mt-1 uppercase">
                        REG - CAMPINAS - SP / ADM - CAMPINAS - SP<br>
                        CNPJ: 46.043.295/0001-84 - IE.: ISENTO<br>
                        Patrimônio - Bens Móveis
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Info Bar -->
        <div class="grid grid-cols-6 gap-0 border-b-2 border-black text-[10px] uppercase font-bold mb-4">
            <div class="p-1 px-2 border-r border-black">
                <span class="opacity-50">Nº:</span> {{ str_pad($inventario->id, 7, '0', STR_PAD_LEFT) }}
            </div>
            <div class="p-1 px-2 border-r border-black">
                <span class="opacity-50">Data:</span> {{ $inventario->created_at->format('d/m/Y') }}
            </div>
            <div class="p-1 px-2 border-r border-black">
                <span class="opacity-50">Início:</span> {{ $inventario->created_at->format('H:i:s') }}
            </div>
            <div class="p-1 px-2 border-r border-black">
                <span class="opacity-50">Fim:</span> {{ $inventario->updated_at->format('H:i:s') }}
            </div>
            <div class="p-1 px-2 col-span-2 text-right">
                <span class="text-lg font-black">{{ number_format($resultado, 2) }}%</span>
            </div>
        </div>

        <div class="flex justify-between text-[11px] font-black uppercase mb-6 border-b border-black pb-1">
            <div><span class="opacity-50">Localidade:</span> {{ str_pad($inventario->id_igreja, 4, '0', STR_PAD_LEFT) }} -
                {{ $inventario->igreja_nome }}
            </div>
            <div><span class="opacity-50">Setor:</span> {{ $inventario->setor ?? '---' }}</div>
        </div>

        @php
            $sections = [
                'cadastrar' => ['title' => 'CADASTRAR (NOVOS)', 'color' => '#004A80'],
                'alterar' => ['title' => 'ALTERAR', 'color' => '#004A80'],
                'excluir' => ['title' => 'EXCLUIR', 'color' => '#004A80'],
                'imprimir' => ['title' => 'IMPRIMIR', 'color' => '#004A80']
            ];
        @endphp

        @foreach($sections as $type => $info)
            @php $items = $inventario->detalhes()->where('tratativa', $type)->with('bem')->get(); @endphp

            @if($items->isNotEmpty())
                <div class="mb-8 break-inside-avoid">
                    <div class="bg-[{{ $info['color'] }}] text-white p-1 text-center text-xs font-black uppercase tracking-[0.3em]">
                        {{ $info['title'] }}
                    </div>
                    <table class="w-full text-[10px] border-collapse border border-black">
                        <thead class="bg-gray-100 font-black uppercase tracking-tighter">
                            <tr>
                                <th class="p-1 px-2 border border-black w-24">Etiqueta</th>
                                <th class="p-1 px-2 border border-black">Descrição</th>
                                <th class="p-1 px-2 border border-black w-32">Dependência</th>
                                <th class="p-1 px-2 border border-black w-40">Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr class="border border-black">
                                    <td class="p-1 px-2 border border-black font-mono font-bold">{{ $item->bem->id_bem }}</td>
                                    <td class="p-1 px-2 border border-black uppercase leading-tight">{{ $item->bem->descricao }}</td>
                                    <td class="p-1 px-2 border border-black text-center uppercase">
                                        {{ $item->dependencia_nome ?? '---' }}
                                    </td>
                                    <td class="p-1 px-2 border border-black font-bold italic">{{ $item->observacao }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-[#004A80] text-white">
                            <tr>
                                <td colspan="3" class="p-1 px-4 text-right font-black uppercase tracking-widest">
                                    {{ $info['title'] }}
                                </td>
                                <td class="p-1 px-4 text-center font-black text-sm">{{ $items->count() }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        @endforeach

        <!-- Signatures -->
        <div class="mt-24 grid grid-cols-2 gap-16 text-center text-[10px] tracking-tight font-bold">
            <div class="flex flex-col items-center">
                <div class="w-full border-t border-black mb-2"></div>
                <p class="uppercase font-black text-xs mb-4 text-blue-900 border-b border-blue-50">Responsáveis
                    (Assinaturas)</p>
                <div class="text-gray-700 italic leading-tight uppercase font-bold text-[11px]">
                    {!! nl2br(e($inventario->responsavel)) !!}
                </div>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-full border-t border-black mb-2"></div>
                <p class="uppercase font-black text-xs mb-4 text-blue-900 border-b border-blue-50">Inventariantes (Equipe)
                </p>
                <div class="text-gray-700 italic leading-tight uppercase font-bold text-[11px]">
                    {!! nl2br(e($inventario->inventariante)) !!}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div
            class="mt-12 flex justify-between items-end border-t border-black pt-1 text-[8px] font-bold text-gray-500 uppercase">
            <div>SIBEM - Sistema de Inventário de Bens Móveis - By Rodrigo Lima© - Todos os direitos reservados - 2026</div>
            <div>Página 1 de 1</div>
        </div>

        <!-- Print Control -->
        <div class="mt-12 flex justify-center print:hidden">
            <button onclick="window.print()"
                class="bg-blue-900 text-white px-12 py-3 rounded-sm font-black tracking-[0.2em] shadow-xl hover:bg-black transition active:scale-95">
                🖨️ EMITIR RELATÓRIO GERAL
            </button>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }

            @page {
                margin: 1cm;
                size: A4;
            }

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
@endsection