@extends('layouts.app')

@section('title', 'Dashboard - SIBEM')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fadeIn">
        <!-- Stat Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M19 5h-2V3a1 1 0 00-1-1h-8a1 1 0 00-1 1v2H5a1 1 0 00-1 1v14a1 1 0 001 1h14a1 1 0 001-1V6a1 1 0 00-1-1zM7 19V7h10v12H7z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Inventários Abertos</p>
            <p class="text-4xl font-bold text-gray-800">3</p>
            <div
                class="mt-4 flex items-center gap-2 text-xs font-medium text-green-600 bg-green-50 w-fit px-2 py-1 rounded-full">
                <span>↑ 2 novos esta semana</span>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 4h16v16H4z" opacity=".3" />
                    <path
                        d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-9 14H7v-4h4v4zm0-6H7V6h4v4zm6 6h-4v-4h4v4zm0-6h-4V6h4v4z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Bens Inventariados</p>
            <p class="text-4xl font-bold text-gray-800">1,248</p>
            <div
                class="mt-4 flex items-center gap-2 text-xs font-medium text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded-full">
                <span>65% do total previsto</span>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Pendências</p>
            <p class="text-4xl font-bold text-gray-800">12</p>
            <div
                class="mt-4 flex items-center gap-2 text-xs font-medium text-orange-600 bg-orange-50 w-fit px-2 py-1 rounded-full">
                <span>Ação necessária</span>
            </div>
        </div>
    </div>

    <!-- Main Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-fadeIn">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-800">Próximos Inventários</h2>
            <button class="text-sm font-bold text-blue-600 hover:text-blue-700">Ver Agenda Completa</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg">Localidade</th>
                        <th class="px-4 py-3">Data Prevista</th>
                        <th class="px-4 py-3">Responsável</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 rounded-r-lg">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-gray-800">Jd. Nova Europa</td>
                        <td class="px-4 py-3">15/02/2026</td>
                        <td class="px-4 py-3">Irmão João</td>
                        <td class="px-4 py-3"><span
                                class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold uppercase">Agendado</span>
                        </td>
                        <td class="px-4 py-3 text-blue-600 font-bold cursor-pointer">Iniciar</td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-semibold text-gray-800">Vila União</td>
                        <td class="px-4 py-3">20/02/2026</td>
                        <td class="px-4 py-3">Irmão Pedro</td>
                        <td class="px-4 py-3"><span
                                class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold uppercase">Pendente</span>
                        </td>
                        <td class="px-4 py-3 text-blue-600 font-bold cursor-pointer">Detalhes</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection