
import React from 'react';

interface SidebarProps {
  currentView: string;
  setView: (view: string) => void;
  onShowCredits: () => void;
  user: { nome: string; email: string };
}

const Sidebar: React.FC<SidebarProps> = ({ currentView, setView, onShowCredits, user }) => {
  const menuGroups = [
    {
      label: 'NAVEGAÇÃO PRINCIPAL',
      items: [
        { id: 'dashboard', label: 'Dashboard', icon: '🏠' },
        { id: 'bens', label: 'Gestão de Bens', icon: '📦' },
        { id: 'inventarios', label: 'Inventários', icon: '📋' },
      ]
    },
    {
      label: 'ADMINISTRAÇÃO',
      items: [
        { id: 'usuarios', label: 'Gerenciar Usuários', icon: '👥' },
        { id: 'localidades', label: 'Localidades', icon: '⛪' },
      ]
    },
    {
      label: 'CONFIGURAÇÃO',
      items: [
        { id: 'config', label: 'Configurações', icon: '⚙️' },
        { id: 'sobre', label: 'Sobre o SIBEM', icon: 'ℹ️', action: onShowCredits },
      ]
    }
  ];

  return (
    <aside className="w-64 bg-white border-r border-gray-200 flex flex-col h-full shrink-0">
      <div className="p-6 border-b flex items-center gap-3">
        <div className="bg-blue-900 text-white p-1 rounded font-black text-xs">SIBEM</div>
        <span className="font-bold text-gray-700 tracking-tight">SIBEM CCB</span>
      </div>

      <nav className="flex-1 overflow-y-auto p-4 space-y-6">
        {menuGroups.map((group) => (
          <div key={group.label}>
            <h3 className="text-[10px] font-bold text-gray-400 mb-2 tracking-wider uppercase px-4">
              {group.label}
            </h3>
            <ul className="space-y-1">
              {group.items.map((item) => (
                <li key={item.id}>
                  <button
                    onClick={() => item.action ? item.action() : setView(item.id)}
                    className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all ${
                      currentView === item.id 
                        ? 'bg-blue-50 text-blue-700 shadow-sm border border-blue-100' 
                        : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'
                    }`}
                  >
                    <span className="text-lg">{item.icon}</span>
                    {item.label}
                  </button>
                </li>
              ))}
            </ul>
          </div>
        ))}
      </nav>

      <div className="p-4 border-t bg-gray-50">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border border-blue-200">
            {user.nome.charAt(0)}
          </div>
          <div className="overflow-hidden">
            <p className="text-sm font-bold text-gray-800 truncate">{user.nome}</p>
            <p className="text-[11px] text-gray-500 truncate">{user.email}</p>
          </div>
        </div>
      </div>
    </aside>
  );
};

export default Sidebar;
