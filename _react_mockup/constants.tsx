
import React from 'react';

export const CREDITS_TEXT = (
  <div className="space-y-4 text-gray-700 leading-relaxed text-sm md:text-base">
    <p>
      Este software, SIBEM CCB - Sistema para Inventário de Bens Móveis, foi desenvolvido com dedicação e zelo para atender exclusivamente às necessidades da Congregação Cristã no Brasil, com o objetivo de proporcionar organização e gestão eficiente de seus bens móveis, sempre guiados pelos princípios cristãos de ordem e responsabilidade.
    </p>
    <p>
      A versão 4 do SIBEM CCB reflete o esforço coletivo de irmãos que se dedicaram a este propósito com amor e compromisso.
    </p>
    <p>
      Agradecemos a Deus por nos conceder sabedoria, força e inspiração para concluir este projeto. Nosso reconhecimento especial vai aos irmãos que contribuíram intelectualmente e tecnicamente, cuja colaboração foi fundamental para o sucesso desta versão.
    </p>
    <p className="font-semibold italic text-blue-800">
      Que este trabalho seja uma ferramenta eficaz na administração dos bens da irmandade, sempre para a glória do Senhor.
    </p>
    <div className="bg-blue-50 p-4 border-l-4 border-blue-600 italic">
      "Tudo quanto fizerdes, fazei-o de todo o coração, como ao Senhor, e não aos homens."<br/>
      (Colossenses 3:23)
    </div>
    <div className="mt-6">
      <p className="font-bold">Vossos irmãos em Cristo,</p>
      <ul className="mt-2 grid grid-cols-2 gap-2">
        <li>Rodrigo Lima</li>
        <li>Jackson Passos</li>
        <li>Marcos Dias</li>
        <li>Marcos Roberto</li>
        <li>Emanoel Oliveira</li>
      </ul>
    </div>
  </div>
);

export const REGIONAL_DATA = [
  { id: 1, nome: 'Adm Regional Campinas' }
];

export const LOCAL_DATA = [
  { id: 101, regionalId: 1, nome: 'Adm Campinas', dbName: 'sibem_cps' },
  { id: 102, regionalId: 1, nome: 'Adm Hortolândia', dbName: 'sibem_horto' },
  { id: 103, regionalId: 1, nome: 'Adm Paulínia', dbName: 'sibem_pau' },
  { id: 104, regionalId: 1, nome: 'Adm Sumaré', dbName: 'sibem_sum' }
];

export const STATUS_MAP = {
  ACTIVE: 1,
  INACTIVE: 0
};
