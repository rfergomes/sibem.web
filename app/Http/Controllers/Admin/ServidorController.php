<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servidor;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServidorController extends Controller
{
    private function checkAccess()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdminSistema()) {
            abort(403, 'Acesso restrito apenas ao administrador do sistema.');
        }
    }

    /**
     * Get a PDO instance for dynamic database testing.
     */
    private function getPdoForServer($host, $port, $database, $username, $password)
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 5, // 5 seconds timeout
        ];
        return new \PDO($dsn, $username, $password, $options);
    }

    public function index(Request $request)
    {
        $this->checkAccess();

        $search = $request->input('search');
        $query = Servidor::with('local.regional')->orderBy('descricao');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                  ->orWhere('servidor', 'like', "%{$search}%")
                  ->orWhere('banco', 'like', "%{$search}%")
                  ->orWhereHas('local', function($ql) use ($search) {
                      $ql->where('adm_local', 'like', "%{$search}%");
                  });
            });
        }

        $servidores = $query->paginate(15);

        // Fetch all locals for creation select
        $locais = Local::orderBy('adm_local')->get();

        return view('admin.servidores.index', compact('servidores', 'locais'));
    }

    public function create()
    {
        $this->checkAccess();
        // Get all locals that do not have a server configured yet
        $locais = Local::whereDoesntHave('servidor')->orderBy('adm_local')->get();
        return view('admin.servidores.create', compact('locais'));
    }

    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'admlc_id' => 'required|integer|unique:mysql_sys.servidores_v2,admlc_id',
            'descricao' => 'required|string|max:255',
            'servidor' => 'required|string|max:255',
            'porta' => 'required|integer|between:1,65535',
            'banco' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'senha' => 'nullable|string|max:255',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->has('ativo') ? 1 : 0;
        $validated['provisionado'] = 0;

        Servidor::create($validated);

        return redirect()->route('admin.servidores.index')->with('success', 'Servidor de banco de dados cadastrado com sucesso.');
    }

    public function edit($id)
    {
        $this->checkAccess();
        $servidor = Servidor::findOrFail($id);
        
        // For editing, list the current local and other locals that don't have a server configured yet
        $locais = Local::where(function($q) use ($servidor) {
            $q->whereDoesntHave('servidor')
              ->orWhere('admlc_id', $servidor->admlc_id);
        })->orderBy('adm_local')->get();

        return view('admin.servidores.edit', compact('servidor', 'locais'));
    }

    public function update(Request $request, $id)
    {
        $this->checkAccess();
        $servidor = Servidor::findOrFail($id);

        if ($servidor->provisionado) {
            // Protected update: only allow changes to description and active status
            $validated = $request->validate([
                'descricao' => 'required|string|max:255',
                'ativo' => 'boolean',
            ]);

            $servidor->update([
                'descricao' => $validated['descricao'],
                'ativo' => $request->has('ativo') ? 1 : 0
            ]);

            return redirect()->route('admin.servidores.index')->with('success', 'Servidor atualizado (campos de conexão protegidos por estar provisionado).');
        }

        $validated = $request->validate([
            'admlc_id' => 'required|integer|unique:mysql_sys.servidores_v2,admlc_id,' . $servidor->id,
            'descricao' => 'required|string|max:255',
            'servidor' => 'required|string|max:255',
            'porta' => 'required|integer|between:1,65535',
            'banco' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'senha' => 'nullable|string|max:255',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->has('ativo') ? 1 : 0;
        $servidor->update($validated);

        return redirect()->route('admin.servidores.index')->with('success', 'Configuração de servidor atualizada com sucesso.');
    }

    public function destroy($id)
    {
        $this->checkAccess();
        $servidor = Servidor::findOrFail($id);

        if ($servidor->provisionado) {
            return back()->with('error', 'Não é possível excluir um servidor que já foi provisionado para evitar órfãos de dados.');
        }

        $servidor->delete();

        return redirect()->route('admin.servidores.index')->with('success', 'Configuração de servidor excluída com sucesso.');
    }

    /**
     * AJAX endpoint to test server connectivity and fetch counts.
     */
    public function testConnection($id)
    {
        $this->checkAccess();
        $server = Servidor::findOrFail($id);

        $start = microtime(true);
        try {
            $pdo = $this->getPdoForServer($server->servidor, $server->porta, $server->banco, $server->usuario, $server->senha);
            $latency = round((microtime(true) - $start) * 1000); // ms

            // Count tables
            $bensCount = 0;
            $inventariosCount = 0;
            $isProvisionedFisicamente = false;

            $stmtBens = $pdo->query("SHOW TABLES LIKE 'bens_v2'");
            $hasBens = $stmtBens->rowCount() > 0;

            $stmtInv = $pdo->query("SHOW TABLES LIKE 'inventarios_v2'");
            $hasInv = $stmtInv->rowCount() > 0;

            $stmtDet = $pdo->query("SHOW TABLES LIKE 'inventario_detalhes_v2'");
            $hasDet = $stmtDet->rowCount() > 0;

            $isProvisionedFisicamente = ($hasBens && $hasInv && $hasDet);

            if ($hasBens) {
                $bensCount = $pdo->query("SELECT COUNT(*) FROM bens_v2")->fetchColumn();
            }
            if ($hasInv) {
                $inventariosCount = $pdo->query("SELECT COUNT(*) FROM inventarios_v2")->fetchColumn();
            }

            // Sync central DB status if physically provisioned
            if ($isProvisionedFisicamente && !$server->provisionado) {
                $server->update([
                    'provisionado' => true,
                    'data_provisionamento' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Conectado com sucesso!',
                'latency' => $latency,
                'bens_count' => (int)$bensCount,
                'inventarios_count' => (int)$inventariosCount,
                'is_provisioned_fisicamente' => $isProvisionedFisicamente,
                'provisionado' => $server->provisionado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Falha de conexão: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * AJAX endpoint to run database provisioning.
     */
    public function provision($id)
    {
        $this->checkAccess();
        $server = Servidor::findOrFail($id);

        if ($server->provisionado) {
            return response()->json([
                'success' => false,
                'message' => 'Este servidor já está marcado como provisionado no banco central.'
            ], 422);
        }

        // Test connection
        try {
            $pdo = $this->getPdoForServer($server->servidor, $server->porta, $server->banco, $server->usuario, $server->senha);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao conectar ao servidor de destino: ' . $e->getMessage()
            ], 422);
        }

        // Check physical tables existence
        $tablesExist = [];
        $requiredTables = ['bens_v2', 'inventarios_v2', 'inventario_detalhes_v2'];

        foreach ($requiredTables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                $tablesExist[] = $table;
            }
        }

        if (!empty($tablesExist)) {
            // Lock out provisioning but update central status to match physical reality
            $server->update([
                'provisionado' => true,
                'data_provisionamento' => $server->data_provisionamento ?? now()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Provisionamento cancelado: As seguintes tabelas já existem fisicamente no banco de destino: ' . implode(', ', $tablesExist) . '. O status do servidor central foi marcado como "Provisionado" por segurança.'
            ], 422);
        }

        $logs = [];
        try {
            $pdo->beginTransaction();

            // Create bens_v2
            $sqlBens = "CREATE TABLE bens_v2 (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                bem_id VARCHAR(20) NOT NULL UNIQUE,
                descricao VARCHAR(255) NOT NULL,
                igreja_id VARCHAR(11) NOT NULL,
                dependencia_id BIGINT UNSIGNED NOT NULL,
                status_id BIGINT UNSIGNED NOT NULL,
                tipo_id BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_igreja (igreja_id),
                INDEX idx_dependencia (dependencia_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $pdo->exec($sqlBens);
            $logs[] = "[OK] Tabela 'bens_v2' criada com sucesso.";

            // Create inventarios_v2
            $sqlInventarios = "CREATE TABLE inventarios_v2 (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                inventario_id VARCHAR(50) NOT NULL UNIQUE,
                igreja_id VARCHAR(11) NOT NULL,
                data DATE NOT NULL,
                responsaveis VARCHAR(600) NULL,
                inventariantes VARCHAR(600) NULL,
                inicio VARCHAR(60) NULL,
                termino VARCHAR(60) NULL,
                tempo TIME NULL,
                situacao VARCHAR(50) NOT NULL,
                bens_inicial INT DEFAULT 0,
                bens_lidos INT DEFAULT 0,
                bens_pendentes INT DEFAULT 0,
                bens_novos INT DEFAULT 0,
                bens_final INT DEFAULT 0,
                bens_importado TINYINT DEFAULT 0,
                teste TINYINT DEFAULT 0,
                siga_ok TINYINT DEFAULT 0,
                pdf VARCHAR(255) NULL,
                admlc_id BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_igreja (igreja_id),
                INDEX idx_admlc (admlc_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $pdo->exec($sqlInventarios);
            $logs[] = "[OK] Tabela 'inventarios_v2' criada com sucesso.";

            // Create inventario_detalhes_v2
            $sqlDetalhes = "CREATE TABLE inventario_detalhes_v2 (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                inventario_id VARCHAR(50) NOT NULL,
                bem_id VARCHAR(20) NOT NULL,
                situacao VARCHAR(50) NOT NULL,
                acao VARCHAR(50) NOT NULL,
                cad_desc VARCHAR(200) NULL,
                dependencia_id BIGINT UNSIGNED NOT NULL,
                observacao VARCHAR(200) NULL,
                cont INT DEFAULT 0,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_inventario (inventario_id),
                INDEX idx_bem (bem_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $pdo->exec($sqlDetalhes);
            $logs[] = "[OK] Tabela 'inventario_detalhes_v2' criada com sucesso.";

            $pdo->commit();

            // Update database
            $server->update([
                'provisionado' => true,
                'data_provisionamento' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Provisionamento concluído com sucesso!',
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return response()->json([
                'success' => false,
                'message' => 'Erro durante execução SQL de provisionamento: ' . $e->getMessage()
            ], 500);
        }
    }
}
