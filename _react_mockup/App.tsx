
import React, { useState } from 'react';
import Sidebar from './components/Sidebar';
import Dashboard from './components/Dashboard';
import CreditsModal from './components/CreditsModal';
import { UserProfile, User, LocalAdm } from './types';
import { LOCAL_DATA } from './constants';

const App: React.FC = () => {
  const [currentView, setView] = useState('dashboard');
  const [isCreditsOpen, setCreditsOpen] = useState(false);
  const [showAdmSwitcher, setShowAdmSwitcher] = useState(false);
  
  // Mock current user
  const [user, setUser] = useState<User>({
    id: 1,
    nome: 'RODRIGO LIMA',
    email: 'admin@sibem.com.br',
    perfil: UserProfile.ADMIN_REGIONAL,
    regionalId: 1,
    localId: 101
  });

  const [currentLocalAdm, setCurrentLocalAdm] = useState<LocalAdm>(LOCAL_DATA[0]);

  const handleSwitchAdm = (adm: LocalAdm) => {
    setCurrentLocalAdm(adm);
    setShowAdmSwitcher(false);
    // Logic to reload context for the specific database/tenant would go here
    console.log(`Switched to: ${adm.dbName}`);
  };

  return (
    <div className="flex h-screen bg-gray-50 overflow-hidden font-sans">
      <Sidebar 
        currentView={currentView} 
        setView={setView} 
        onShowCredits={() => setCreditsOpen(true)}
        user={user}
      />

      <main className="flex-1 flex flex-col min-w-0 overflow-hidden">
        {/* Top Header */}
        <header className="h-14 bg-[#111827] flex items-center justify-between px-6 shrink-0 shadow-lg text-white">
          <div className="flex items-center gap-6">
            <div className="flex items-center gap-2">
              <span className="text-xs font-bold text-gray-400 uppercase tracking-widest">ADM</span>
              <span className="text-sm font-bold tracking-tight">{currentLocalAdm.nome.toUpperCase()} - SP</span>
            </div>
            <div className="h-4 w-[1px] bg-gray-700"></div>
            <div className="flex items-center gap-2">
              <div className="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
              <span className="text-[10px] text-gray-400">Conectado: <span className="text-gray-200">{currentLocalAdm.dbName}</span></span>
            </div>
          </div>

          <div className="flex items-center gap-6">
            <button 
              onClick={() => setShowAdmSwitcher(true)}
              className="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-widest"
            >
              Trocar Administração
            </button>
            <div className="h-4 w-[1px] bg-gray-700"></div>
            <button className="text-xs font-bold text-gray-400 hover:text-white transition-colors uppercase tracking-widest">Sair</button>
          </div>
        </header>

        {/* View Content */}
        <div className="flex-1 overflow-y-auto">
          {currentView === 'dashboard' && <Dashboard />}
          {currentView !== 'dashboard' && (
            <div className="flex items-center justify-center h-full text-gray-400 animate-pulse">
              Em breve: Módulo de {currentView.toUpperCase()}
            </div>
          )}
        </div>

        {/* Footer */}
        <footer className="h-10 bg-[#111827] border-t border-gray-800 flex items-center justify-between px-6 shrink-0 text-[10px] text-gray-400 font-medium">
          <div>SIBEM CCB© v4.1.0 — 2020/2026 — Todos os direitos reservados</div>
          <div className="flex items-center gap-4">
             <span>04-02-2026 17:45</span>
             <span className="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
             <span className="uppercase">{user.nome}</span>
          </div>
        </footer>
      </main>

      {/* Modals */}
      <CreditsModal isOpen={isCreditsOpen} onClose={() => setCreditsOpen(false)} />

      {/* Switch Adm Modal */}
      {showAdmSwitcher && (
        <div className="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
          <div className="bg-white rounded-xl shadow-2xl w-full max-w-lg animate-scaleIn">
            <div className="p-6 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
              <h2 className="text-xl font-bold text-gray-800">Selecionar Administração</h2>
              <button onClick={() => setShowAdmSwitcher(false)} className="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div className="p-6 max-h-[60vh] overflow-y-auto space-y-2">
              <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Administrações Permitidas (Escopo Regional)</p>
              {LOCAL_DATA.map((adm) => (
                <button
                  key={adm.id}
                  onClick={() => handleSwitchAdm(adm)}
                  className={`w-full text-left p-4 rounded-xl border transition-all flex items-center justify-between group ${
                    currentLocalAdm.id === adm.id 
                      ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20' 
                      : 'border-gray-100 hover:border-blue-300 hover:bg-gray-50'
                  }`}
                >
                  <div>
                    <span className="block font-bold text-gray-800">{adm.nome}</span>
                    <span className="text-[10px] text-gray-500 uppercase font-bold tracking-tighter">Banco: {adm.dbName}</span>
                  </div>
                  {currentLocalAdm.id === adm.id ? (
                    <span className="text-blue-600 font-bold text-xs uppercase bg-blue-100 px-2 py-1 rounded">Ativo</span>
                  ) : (
                    <span className="opacity-0 group-hover:opacity-100 text-blue-500 transition-opacity">→</span>
                  )}
                </button>
              ))}
            </div>
            <div className="p-4 border-t bg-gray-50 rounded-b-xl flex justify-end">
              <button onClick={() => setShowAdmSwitcher(false)} className="px-6 py-2 text-gray-500 font-bold hover:text-gray-700">Fechar</button>
            </div>
          </div>
        </div>
      )}

      {/* Versículo Popup (Projeto Futuro) */}
      <div className="fixed bottom-14 right-6 animate-slideUp">
         <div className="bg-white rounded-lg shadow-xl p-4 border border-blue-100 max-w-xs relative">
            <div className="absolute -top-2 left-4 w-4 h-4 bg-white border-t border-l border-blue-100 rotate-45"></div>
            <p className="text-xs italic text-gray-600 leading-relaxed mb-2">
              "Lâmpada para os meus pés é tua palavra, e luz para o meu caminho."
            </p>
            <p className="text-[10px] font-bold text-blue-900 text-right">Salmos 119:105</p>
         </div>
      </div>

      <style>{`
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        .animate-scaleIn { animation: scaleIn 0.3s ease-out; }
        .animate-slideUp { animation: slideUp 0.5s ease-out; }
      `}</style>
    </div>
  );
};

export default App;
