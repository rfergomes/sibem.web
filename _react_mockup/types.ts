
export enum UserProfile {
  ADMIN_SISTEMA = 'Administrador do Sistema',
  ADMIN_REGIONAL = 'Administrador Regional',
  ADMIN_LOCAL = 'Administrador Local',
  OPERADOR = 'Operador',
  AUDITOR = 'Auditor'
}

export interface RegionalAdm {
  id: number;
  nome: string;
}

export interface LocalAdm {
  id: number;
  regionalId: number;
  nome: string;
  dbName: string;
}

export interface Localidade {
  id: number;
  localId: number;
  nome: string;
  tipo: 'Casa de Oração' | 'Estacionamento' | 'Oficina de Costura' | 'Serralheria' | 'Barracão';
  endereco: string;
}

export interface Bem {
  id_bem: string; // 12 digits barcode
  descricao: string;
  id_igreja: string;
  id_dependencia: number;
  id_status: number; // 0: Inativo, 1: Ativo
}

export interface User {
  id: number;
  nome: string;
  email: string;
  perfil: UserProfile;
  regionalId?: number;
  localId?: number;
}
