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

### 3. Links Simbólicos e Permissões
Garantir que os uploads e avatares funcionem e que as pastas tenham permissões corretas:
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### 4. Gerenciamento de Bancos (Multi-Tenancy)
O SIBEM utiliza uma estrutura onde cada localidade possui seu próprio banco de dados de inventários (tenants).
- O banco principal (`sibem_global` por padrão) contém dados globais (igrejas, usuários, locais).
- Os bancos tenants são configurados dinamicamente via `TenancyMiddleware`.

**Importante:** Certifique-se de que o usuário do banco no `.env` tenha permissões `GRANT ALL PRIVILEGES` ou pelo menos permissão para acessar todos os bancos de dados dos tenants configurados na tabela `locais`.

### 5. Notificações Push & SSL
As notificações WebPush **exigem** HTTPS em produção. Sem SSL, o Service Worker não será registrado.
Para gerar as chaves VAPID:
```bash
php artisan webpush:vapid
```

### 6. Tarefas Agendadas (Cron)
Para notificações de inventários abertos e outras automações, adicione o seguinte ao Crontab do servidor:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Licença
Este sistema é de uso restrito e confidencial.
