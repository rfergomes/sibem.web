<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Models\Regional;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;

class LocalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Local::query();

        // 1. Authorization Scopes
        if ($user->perfil_id == 1) {
            // Admin Sistema: View All
            if ($request->filled('regional_id')) {
                $query->where('regional_id', $request->regional_id);
            }
        } elseif ($user->perfil_id == 2) {
            // Admin Regional: View only their Regional
            $query->where('regional_id', $user->regional_id);
        } else {
            // Admin Local / Operador: View only Authorized Locais
            // Although usually they don't "manage" locales, they might view the list.
            $localIds = $user->authorized_locais->pluck('id');
            $query->whereIn('id', $localIds);
        }

        // 2. Filters
        // Regional filter is handled above for Admin Sistema. 
        // For others, it's either implicit or not applicable.

        if ($request->filled('uf')) {
            $query->where('uf', $request->uf);
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'like', "%{$request->cidade}%");
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('cidade', 'like', "%{$search}%")
                    ->orWhere('db_name', 'like', "%{$search}%");
            });
        }

        // 3. Sorting
        $query->orderBy('nome');

        // 4. Data for View
        $locais = $query->with('regional')->withCount('igrejas')->paginate(12)->withQueryString();

        // Data for Filters
        $regionais = [];
        if ($user->perfil_id == 1) {
            $regionais = Regional::where('active', true)->orderBy('nome')->get();
        }

        $ufs = $this->getUfs();

        return view('admin.locais.index', compact('locais', 'regionais', 'ufs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Local::class);

        $user = auth()->user();
        $query = Regional::where('active', true);

        if ($user->perfil_id == 2) {
            $query->where('id', $user->regional_id);
        }

        $regionais = $query->orderBy('nome')->get();
        $ufs = $this->getUfs();

        return view('admin.locais.create', compact('regionais', 'ufs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TenantProvisioningService $provisioner)
    {
        $this->authorize('create', Local::class);
        $user = auth()->user();

        // Strict Validation Rules
        $rules = [
            'nome' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionais,id', // Basic check
            'db_host' => 'required|string',
            'db_name' => 'required|string|unique:locais,db_name',
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
            'active' => 'boolean',
            'uf' => 'required|string|size:2',
            'cidade' => 'required|string|max:255',
        ];

        // Custom Validation for Hierarchy
        if ($user->perfil_id == 2 && $request->regional_id != $user->regional_id) {
            abort(403, 'Você só pode criar administrações na sua própria regional.');
        }

        $validated = $request->validate($rules);

        // Retrieve Regional to check UF
        $regional = Regional::findOrFail($validated['regional_id']);

        // Strict UF Rule: Local UF must match Regional UF (if Regional has specific UF)
        // Adjust logic based on business rule: "Regional também estará com uf SP"
        if ($regional->uf && $regional->uf != $validated['uf']) {
            return back()->withErrors(['uf' => "A UF da Administração ({$validated['uf']}) deve ser igual à UF da Regional ({$regional->uf})."])->withInput();
        }

        $local = Local::create($validated);

        if ($request->has('provision')) {
            try {
                $provisioner->provision($local);
                $message = 'Administração criada e banco de dados inicializado com sucesso!';
            } catch (\Exception $e) {
                $message = 'Administração criada, mas falhou ao inicializar banco: ' . $e->getMessage();
                return redirect()->route('locais.index')->with('warning', $message);
            }
        } else {
            $message = 'Administração criada com sucesso!';
        }

        return redirect()->route('locais.index')->with('success', $message);
    }

    private function getUfs()
    {
        return [
            ['sigla' => 'AC', 'nome' => 'Acre'],
            ['sigla' => 'AL', 'nome' => 'Alagoas'],
            ['sigla' => 'AP', 'nome' => 'Amapá'],
            ['sigla' => 'AM', 'nome' => 'Amazonas'],
            ['sigla' => 'BA', 'nome' => 'Bahia'],
            ['sigla' => 'CE', 'nome' => 'Ceará'],
            ['sigla' => 'DF', 'nome' => 'Distrito Federal'],
            ['sigla' => 'ES', 'nome' => 'Espírito Santo'],
            ['sigla' => 'GO', 'nome' => 'Goiás'],
            ['sigla' => 'MA', 'nome' => 'Maranhão'],
            ['sigla' => 'MT', 'nome' => 'Mato Grosso'],
            ['sigla' => 'MS', 'nome' => 'Mato Grosso do Sul'],
            ['sigla' => 'MG', 'nome' => 'Minas Gerais'],
            ['sigla' => 'PA', 'nome' => 'Pará'],
            ['sigla' => 'PB', 'nome' => 'Paraíba'],
            ['sigla' => 'PR', 'nome' => 'Paraná'],
            ['sigla' => 'PE', 'nome' => 'Pernambuco'],
            ['sigla' => 'PI', 'nome' => 'Piauí'],
            ['sigla' => 'RJ', 'nome' => 'Rio de Janeiro'],
            ['sigla' => 'RN', 'nome' => 'Rio Grande do Norte'],
            ['sigla' => 'RS', 'nome' => 'Rio Grande do Sul'],
            ['sigla' => 'RO', 'nome' => 'Rondônia'],
            ['sigla' => 'RR', 'nome' => 'Roraima'],
            ['sigla' => 'SC', 'nome' => 'Santa Catarina'],
            ['sigla' => 'SP', 'nome' => 'São Paulo'],
            ['sigla' => 'SE', 'nome' => 'Sergipe'],
            ['sigla' => 'TO', 'nome' => 'Tocantins'],
        ];
    }



    /**
     * Provision an existing local.
     */
    public function provision(Local $local, TenantProvisioningService $provisioner)
    {
        // Validar se as credenciais do banco foram informadas
        if (empty($local->db_host) || empty($local->db_name) || empty($local->db_user)) {
            return back()->with('error', 'Não é possível provisionar: credenciais do banco de dados não foram informadas. Por favor, edite a administração e preencha os dados de conexão.');
        }

        try {
            $provisioner->provision($local);
            return back()->with('success', 'Banco de dados reinicializado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao inicializar banco: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Local $local)
    {
        $regionais = Regional::all();
        return view('admin.locais.edit', compact('local', 'regionais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Local $local)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'regional_id' => 'required|exists:regionais,id',
            'db_host' => 'required|string',
            'db_name' => 'required|string|unique:locais,db_name,' . $local->id,
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $local->update($validated);

        return redirect()->route('locais.index')->with('success', 'Administração atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Local $local)
    {
        $this->authorize('delete', $local);
        $local->delete();
        return redirect()->route('locais.index')->with('success', 'Administração removida com sucesso.');
    }

    /**
     * Test database connection with provided credentials.
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            // Create a temporary PDO connection to test credentials
            $dsn = "mysql:host={$validated['db_host']};dbname={$validated['db_name']};charset=utf8mb4";
            $pdo = new \PDO(
                $dsn,
                $validated['db_user'],
                $validated['db_password'] ?? '',
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 5, // 5 second timeout
                ]
            );

            // Test a simple query
            $pdo->query('SELECT 1');

            return response()->json([
                'success' => true,
                'message' => 'Conexão estabelecida com sucesso! As credenciais estão corretas.'
            ]);

        } catch (\PDOException $e) {
            $errorMessage = $e->getMessage();

            // Provide user-friendly error messages
            if (str_contains($errorMessage, 'Access denied')) {
                $message = 'Acesso negado. Verifique o usuário e senha do banco de dados.';
            } elseif (str_contains($errorMessage, 'Unknown database')) {
                $message = 'Banco de dados não encontrado. Verifique o nome do banco.';
            } elseif (str_contains($errorMessage, 'Connection refused') || str_contains($errorMessage, 'timed out')) {
                $message = 'Não foi possível conectar ao servidor. Verifique o host e se o servidor MySQL está ativo.';
            } else {
                $message = 'Erro ao conectar: ' . $errorMessage;
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage()
            ]);
        }
    }
}
