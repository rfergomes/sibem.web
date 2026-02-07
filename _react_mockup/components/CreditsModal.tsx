
import React from 'react';
import { CREDITS_TEXT } from '../constants';

interface CreditsModalProps {
  isOpen: boolean;
  onClose: () => void;
}

const CreditsModal: React.FC<CreditsModalProps> = ({ isOpen, onClose }) => {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div className="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto flex flex-col">
        <div className="p-6 border-b flex justify-between items-center bg-gray-50 sticky top-0 z-10">
          <div className="flex items-center gap-4">
            <div className="bg-blue-900 text-white p-2 rounded-lg font-bold text-xl">SIBEM</div>
            <h2 className="text-xl font-bold text-gray-800">Versão 4</h2>
          </div>
          <button 
            onClick={onClose}
            className="text-gray-500 hover:text-red-600 transition-colors p-2 text-2xl"
          >
            &times;
          </button>
        </div>
        <div className="p-8">
          <div className="mb-6 flex justify-center">
             <div className="border-2 border-gray-200 p-2 text-center uppercase text-[10px] leading-tight font-bold w-48">
                Congregação Cristã<br/>No Brasil
             </div>
          </div>
          {CREDITS_TEXT}
        </div>
        <div className="p-4 border-t bg-gray-50 flex justify-end sticky bottom-0 z-10">
          <button 
            onClick={onClose}
            className="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-colors font-medium"
          >
            Fechar
          </button>
        </div>
      </div>
    </div>
  );
};

export default CreditsModal;
