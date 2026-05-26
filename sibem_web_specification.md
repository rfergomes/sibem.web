# SIBEM Web - System Specification & Developer Blueprint

This document acts as a comprehensive, production-ready developer prompt and architectural blueprint to rewrite the **SIBEM Desktop (VB.NET/SQLite)** system into a modern, responsive, and secure **Laravel 12+ Web Application**.

---

## 1. Technical Stack & Architecture

- **Backend Framework:** Laravel 12+ (PHP 8.2+)
- **Database:** MySQL 8.0+ (using Eloquent ORM mapping to the existing schema)
- **Frontend CSS:** Bootstrap 5.3 (Fully responsive, dark mode support ready)
- **JavaScript & Utilities:**
  - **SweetAlert2** (Premium modal confirmations)
  - **Toastr.js** (Non-blocking notification bubbles)
  - **Html5-qrcode** / **QuaggaJS** (Mobile camera barcode scanning library)
  - **Vanilla JS / Axios** (Asynchronous operations, API requests)
- **Security & Auth:**
  - Laravel Breeze/Jetstream or custom Auth system.
  - **Spatie Laravel-Permission** for RBAC (Role-Based Access Control).
  - CSRF protection on all forms/requests.
  - Secure SMTP server configuration for transactional emails (Password resets, notifications).

---

## 2. Multi-Tenant Database Architecture & Eloquent Model Mapping

The system uses the **existing MySQL multi-tenant database structure** divided into a global administration database and multiple specific tenant databases (e.g. one for each local administration: `sibem_cps`, `sibem_horto`, etc.).

### A. Global Database (`sibem_adm`)
Stores configuration, metadata, user access, and central registry.

1. **`admlcs_v2` (Local Administrations) $\rightarrow$ `Admlc` Model**
   - `id` (BIGINT UNSIGNED, PK)
   - `admlc_id` (BIGINT UNSIGNED, UNIQUE legacy ID)
   - `adm_local` (VARCHAR 200), `razao_social` (VARCHAR 200), `cnpj` (VARCHAR 18)
   - `cidade` (VARCHAR 200), `uf` (VARCHAR 2), `status_id` (BIGINT UNSIGNED), `admrg_id` (BIGINT UNSIGNED)

2. **`admrgs_v2` (Regional Administrations) $\rightarrow$ `Admrg` Model**
   - `id` (BIGINT UNSIGNED, PK), `admrg_id` (BIGINT UNSIGNED, UNIQUE)
   - `adm_regional` (VARCHAR 200), `uf` (VARCHAR 2)

3. **`setores_v2` (Sectors) $\rightarrow$ `Setor` Model**
   - `id` (BIGINT UNSIGNED, PK)
   - `cod_setor` (VARCHAR 3), `descricao` (VARCHAR 60), `admlc_id` (BIGINT UNSIGNED)

4. **`igrejas_v2` (Churches/Templo) $\rightarrow$ `Igreja` Model**
   - `id` (BIGINT UNSIGNED, PK), `igreja_id` (VARCHAR 11 - legacy ID)
   - `igreja` (VARCHAR 200), `cod_siga` (VARCHAR 20), `razao_social` (VARCHAR 200)
   - `cnpj` (VARCHAR 20), `logradouro` (VARCHAR 200), `numero` (VARCHAR 20), `bairro` (VARCHAR 160)
   - `cidade` (VARCHAR 200), `uf` (VARCHAR 2), `tipo_id` (BIGINT UNSIGNED), `status_id` (BIGINT UNSIGNED)
   - `cod_setor` (VARCHAR 11), `admlc_id` (BIGINT UNSIGNED), `observacao` (VARCHAR 200)

5. **`users_v2` (Users) $\rightarrow$ `User` Model**
   - `id` (BIGINT UNSIGNED, PK), `name` (VARCHAR 200), `email` (VARCHAR 100), `password` (VARCHAR 200)
   - `senha_salt` (VARCHAR 200 - legacy), `admlc_id` (BIGINT UNSIGNED), `tipo` (VARCHAR 30), `foto` (VARCHAR 200)

6. **`servidores_v2` (Tenant Server Configs) $\rightarrow$ `Servidor` Model**
   - `id` (BIGINT UNSIGNED, PK), `admlc_id` (BIGINT UNSIGNED)
   - `servidor` (VARCHAR 200), `porta` (VARCHAR 11), `banco` (VARCHAR 60), `usuario` (VARCHAR 60), `senha` (VARCHAR 60), `ativo` (TINYINT)

7. **`dependencias_v2` (Global Dependencies) $\rightarrow$ `Dependencia` Model**
   - `id` (BIGINT UNSIGNED, PK), `dependencia_id` (BIGINT UNSIGNED, UNIQUE), `descricao` (VARCHAR 250)

8. **`bens_tipos_v2` (Global Asset Types) $\rightarrow$ `BemTipo` Model**
   - `id` (BIGINT UNSIGNED, PK), `descricao` (VARCHAR 200), `conta_contabil` (INT)

### B. Tenant Databases (e.g., `sibem_cps`, `sibem_horto`)
Stores specific transactional inventory tables.

1. **`bens_v2` (Assets) $\rightarrow$ `Bem` Model**
   - `id` (BIGINT UNSIGNED, PK), `bem_id` (VARCHAR 20, UNIQUE barcode)
   - `descricao` (VARCHAR 255), `igreja_id` (VARCHAR 11), `dependencia_id` (BIGINT UNSIGNED)
   - `status_id` (BIGINT UNSIGNED), `tipo_id` (BIGINT UNSIGNED)

2. **`inventarios_v2` (Inventories) $\rightarrow$ `Inventario` Model**
   - `id` (BIGINT UNSIGNED, PK), `inventario_id` (VARCHAR 50, UNIQUE)
   - `igreja_id` (VARCHAR 11), `data` (VARCHAR 255), `responsaveis` (VARCHAR 600), `inventariantes` (VARCHAR 600)
   - `inicio` (VARCHAR 60), `termino` (VARCHAR 60), `tempo` (TIME), `situacao` (VARCHAR 50)
   - `bens_inicial` (INT), `bens_lidos` (INT), `bens_pendentes` (INT), `bens_novos` (INT), `bens_final` (INT)
   - `bens_importado` (TINYINT), `teste` (TINYINT), `siga_ok` (TINYINT), `pdf` (VARCHAR 255), `admlc_id` (BIGINT UNSIGNED)

3. **`inventario_detalhes_v2` (Inventory Details) $\rightarrow$ `InventarioDetalhes` Model**
   - `id` (BIGINT UNSIGNED, PK), `inventario_id` (VARCHAR 50), `bem_id` (VARCHAR 20)
   - `situacao` (VARCHAR 50), `acao` (VARCHAR 50), `cad_desc` (VARCHAR 200)
   - `dependencia_id` (BIGINT UNSIGNED), `observacao` (VARCHAR 200), `cont` (INT)

---

## 3. Dynamic Tenancy Connection Switching in Laravel 12+

To seamlessly handle multiple databases without modifying the existing MySQL architecture:

1. **Database Configuration (`config/database.php`):**
   Define two connection profiles: `mysql_sys` (pointing statically to `sibem_adm`) and `tenant` (configured dynamically).
   ```php
   'mysql_sys' => [
       'driver' => 'mysql',
       'host' => env('DB_HOST', 'sibem-adm.mysql.uhserver.com'),
       'database' => 'sibem_adm',
       // ...
   ],
   'tenant' => [
       'driver' => 'mysql',
       'host' => '',
       'database' => '',
       // Will be overridden dynamically
   ]
   ```

2. **Dynamic Tenancy Middleware:**
   Interceptors read the authenticated user's `admlc_id`, fetch connection parameters from `servidores_v2`, and update the config values at runtime:
   ```php
   $server = DB::connection('mysql_sys')
       ->table('servidores_v2')
       ->where('admlc_id', auth()->user()->admlc_id)
       ->where('ativo', 1)
       ->first();

   if ($server) {
       config([
           'database.connections.tenant.host' => $server->servidor,
           'database.connections.tenant.port' => $server->porta,
           'database.connections.tenant.database' => $server->banco,
           'database.connections.tenant.username' => $server->usuario,
           'database.connections.tenant.password' => $server->senha,
       ]);
       
       DB::purge('tenant');
   }
   ```

3. **Model Connection Declarations:**
   - Central models (`User`, `Igreja`, `Setor`, `Admlc`, `Servidor`, etc.) explicitly declare: `protected $connection = 'mysql_sys';`
   - Tenant transactional models (`Bem`, `Inventario`, `InventarioDetalhes`) declare: `protected $connection = 'tenant';`

---

## 4. User Roles & Permission Matrix (Spatie RBAC)

Implement Spatie's Role & Permission package with the following hierarchy:

| Role | Scope | Key Permissions |
| :--- | :--- | :--- |
| **Super Admin** | Global (All Admins) | Create/Edit Regionals and Locals, Manage System Settings, Read/Write all data. |
| **Regional Coordinator** | Regional level | Read-only access to all Locals in their Regional, run consolidated reports. |
| **Local Admin** | Local Admin level | Full CRUD on Sectors, Churches, Assets, Inventories, and Users under their specific Local Admin scope. |
| **Inventory Clerk** | Church level | Start, run, edit, scan barcodes, and finalize inventories. No delete access on assets or admin configuration. |

### Global Scopes & Multi-Tenancy
All Eloquent models (except `RegionalAdmin`) must implement a **Multi-Tenancy Global Scope** based on `auth()->user()->admlc_id`. Users must never query or mutate data belonging to other local administrations.

---

## 5. Front-End Guidelines & UX/UI Framework

1. **Responsive Navbar:** Collapsible side/top navigation menu optimized for smartphones, tablets, and desktops.
2. **Dashboard UI Cards:** Clean cards showing summary boxes (Sectors, Active Churches, Inactive, Departments, Inventories) utilizing Bootstrap grid system.
3. **DataTables integration:** Fast, client-side pagination, global searching, and filtering on table lists (churches, assets, inventories).
4. **Action Confirmations (SweetAlert2):** Triggered on deletions, changes of status, or finalizing an inventory.
   ```javascript
   Swal.fire({
       title: 'Finalizar Inventário?',
       text: "Esta ação consolidará as leituras e não poderá ser desfeita!",
       icon: 'warning',
       showCancelButton: true,
       confirmButtonColor: '#3085d6',
       cancelButtonColor: '#d33',
       confirmButtonText: 'Sim, finalizar!'
   }).then((result) => { if (result.isConfirmed) { /* Submit form */ } });
   ```
5. **Real-time Notifications (Toastr):** Instant feedbacks on successful reads, saving settings, or connection warnings.

---

## 6. Barcode Scanning Implementation

To achieve 100% functionality on mobile devices and desktops, implement a dual-input scanning mechanism:

### A. Mobile Camera Scanner (HTML5/JS)
Embed a scanning window on the inventory detail screen using **Html5-qrcode** (runs on mobile browsers over HTTPS without requiring native apps).
```html
<div id="reader" style="width: 100%; max-width: 500px; margin: auto;"></div>
```
```javascript
const html5QrCode = new Html5Qrcode("reader");
const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    // Process code read asynchronously
    axios.post(`/inventarios/${inventoryId}/scan`, { barcode: decodedText })
         .then(res => {
             toastr.success(`Item lido: ${res.data.descricao}`);
             refreshInventoryGrid();
         })
         .catch(err => toastr.error(err.response.data.message));
};
const config = { fps: 10, qrbox: { width: 250, height: 150 } };
html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);
```

### B. External Physical Scanner Input (USB/Bluetooth)
An invisible text field listener on the main scan page catches physical scanner inputs. Physical readers behave like rapid keyboards ending with an `Enter` carriage return.
```javascript
let barcodeBuffer = "";
document.addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        if (barcodeBuffer.length >= 4) {
            axios.post(`/inventarios/${inventoryId}/scan`, { barcode: barcodeBuffer })
                 .then(res => {
                     toastr.success(`Lido via Leitor Externo: ${res.data.descricao}`);
                     refreshInventoryGrid();
                 });
            barcodeBuffer = "";
        }
    } else {
        if (e.key !== "Shift") barcodeBuffer += e.key;
    }
});
```

---

## 7. Advanced Features & Business Logic Rules

### A. Real-time Inventory Calculation
The view logic of inventory progress is based on the corrected SQLite `CROSS JOIN` query (mapped to MySQL query).
- When an inventory is created, a snapshot of all active assets in that church is loaded into `inventario_detalhes` with a status of `PENDENTE`.
- As barcodes are scanned:
  - If the barcode matches a registered asset of the church, set the status to `OK` and action to `MANTER`.
  - If the barcode is not in the church's assets:
    - Open a Toastr modal prompting the user to select whether they want to **CADASTRAR** (as new asset), **TRANSFERIR** (from another church), or discard it.
- **Completeness Calculation:**
  $$\text{Realizados \%} = \frac{\text{Itens Lidos}}{\text{Total de Itens Ativos da Igreja}}$$

### B. System Health & Connection Handler
For any task requiring outbound syncing (e.g. updating cloud matrices), implement standard Laravel **Guzzle/HTTP Clients** wrapped inside queueable **Laravel Jobs** with high retry capability.
- Connection errors must be logged in a custom log channel.
- Users must get clear UI Toastr warnings if remote cloud syncing is offline, while local operations continue seamlessly.

### C. Security Best Practices
1. **Sanitized Queries:** Prevent SQL injection using standard Eloquent queries. Never concatenate raw user input into `whereRaw`.
2. **CSRF Validation:** Verify all Axios/Form requests have `X-CSRF-TOKEN` headers.
3. **Password Security:** Use standard `bcrypt` / `argon2id` via Laravel's built-in hashing engine.
4. **Session Lifetime:** Set appropriate timeout configuration in `config/session.php`.

