
import React from 'react';

const Dashboard: React.FC = () => {
  const stats = [
    { label: 'TOTAL DE BENS', value: '3', sub: 'Ativos cadastrados nesta administração.', color: 'text-blue-600', border: 'border-blue-500' },
    { label: 'INVENTÁRIOS ABERTOS', value: '0', sub: 'Processos de inventário em andamento.', color: 'text-emerald-600', border: 'border-emerald-500' },
    { label: 'LOCALIDADES', value: '15', sub: 'Casas de oração e outros espaços.', color: 'text-orange-600', border: 'border-orange-500' }
  ];

  return (
    <div className="p-8 space-y-8 animate-fadeIn">
      <div className="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
        <h1 className="text-2xl font-bold text-gray-800 mb-2 tracking-tight">Bem-vindo ao SIBEM</h1>
        <p className="text-gray-500">Utilize o menu lateral para gerenciar os bens móveis e realizar inventários.</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {stats.map((stat) => (
          <div key={stat.label} className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div className={`absolute left-0 top-0 bottom-0 w-1 ${stat.border.replace('border-', 'bg-')}`}></div>
            <p className="text-[10px] font-bold text-gray-400 mb-4 tracking-widest uppercase">{stat.label}</p>
            <p className={`text-4xl font-black ${stat.color} mb-3`}>{stat.value}</p>
            <p className="text-xs text-gray-500 font-medium leading-relaxed">{stat.sub}</p>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
          <h2 className="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
             <span className="p-2 bg-blue-50 rounded-lg">📊</span> Estatísticas de Inventário
          </h2>
          <div className="h-48 flex items-end gap-3 justify-between px-4">
             {[40, 70, 45, 90, 65, 80, 50, 60, 100, 30].map((h, i) => (
               <div key={i} className="flex-1 bg-gray-100 rounded-t-lg group relative">
                 <div 
                   className="absolute bottom-0 left-0 right-0 bg-blue-500 rounded-t-lg transition-all duration-700 ease-out group-hover:bg-blue-600" 
                   style={{ height: `${h}%` }}
                 ></div>
               </div>
             ))}
          </div>
          <div className="mt-4 flex justify-between text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
            <span>Jan</span><span>Mar</span><span>Mai</span><span>Jul</span><span>Set</span><span>Nov</span>
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
          <h2 className="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span className="p-2 bg-purple-50 rounded-lg">📅</span> Próximos Inventários
          </h2>
          <div className="space-y-4">
            <div className="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer border border-transparent hover:border-gray-200">
               <div className="text-center min-w-[50px]">
                 <p className="text-[10px] font-bold text-gray-400">FEV</p>
                 <p className="text-xl font-black text-gray-800">12</p>
               </div>
               <div className="flex-1">
                 <p className="font-bold text-gray-800 text-sm">C.O. JARDIM DO LAGO II</p>
                 <p className="text-xs text-gray-500">Agendado por: Rodrigo Lima</p>
               </div>
               <span className="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded uppercase tracking-tighter">Sinalizado</span>
            </div>
            <div className="flex items-center gap-4 p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer border border-transparent hover:border-gray-200 opacity-60">
               <div className="text-center min-w-[50px]">
                 <p className="text-[10px] font-bold text-gray-400">FEV</p>
                 <p className="text-xl font-black text-gray-800">15</p>
               </div>
               <div className="flex-1">
                 <p className="font-bold text-gray-800 text-sm">C.O. CAMPO BELO</p>
                 <p className="text-xs text-gray-500">Aguardando confirmação</p>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
