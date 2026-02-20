# SIBEM - Sistema Informatizado de Bens Móveis

Sistema para gestão de inventários e bens móveis da Congregação Cristã no Brasil.

## Tecnologias
- Laravel 10 (PHP 8.1+)
- MySQL (Arquitetura Multi-Tenant)
- Tailwind CSS & Alpine.js
- Chart.js (Dashboard BI)
- Gemini API (Devocional Diário)
- WebPush Notifications

## Instalação (Desenvolvimento)
1. Clone o repositório
2. Execute `composer install` e `npm install`
3. Copie o `.env.example` para `.env` e configure as credenciais do banco
4. Gere a chave: `php artisan key:generate`
5. Execute as migrações: `php artisan migrate`
6. Inicie o servidor: `php artisan serve` e `npm run dev`

## Deploy em Produção (Hospedagem Cloud)

### 1. Preparação do Ambiente
Certifique-se de que o servidor possui PHP 8.1+, MySQL e suporte a HTTPS (necessário para WebPush).

### 2. Otimização do Laravel
Execute os seguintes comandos no servidor para máxima performance:
```bash
composer install --optimize-autoloader --no-dev
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Links Simbólicos
Garantir que os uploads e avatares funcionem:
```bash
php artisan storage:link
```

### 4. Gerenciamento de Bancos (Multi-Tenancy)
O SIBEM utiliza uma estrutura onde cada localidade possui seu próprio banco de dados de inventários (tenants).
- O banco principal (`mysql`) contém dados globais (igrejas, usuários, locais).
- Os bancos tenants são configurados dinamicamente via `TenancyMiddleware`.

Certifique-se de que o usuário do banco no `.env` tenha permissão para acessar/criar os bancos de dados dos tenants.

### 5. Notificações Push
Para o funcionamento das notificações em background, configure as chaves VAPID no `.env`:
```bash
php artisan webpush:vapid
```

## Licença
Este sistema é de uso restrito e confidencial.
