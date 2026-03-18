<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Appointment;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param GeminiService $gemini
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @param GeminiService $gemini
     * @param \App\Services\DashboardService $dashboardService
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, GeminiService $gemini, \App\Services\DashboardService $dashboardService)
    {
        // Fetch daily verse/devotional data (Cached by service)
        $dailyData = $gemini->getDailyData();

        // Fetch next appointments
        $nextAppointments = Appointment::with('local')
            ->whereIn('status', ['previsao', 'confirmado'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Determine user profile and fetch stats
        $user = auth()->user();
        $stats = [];

        // Load profile relationship if not loaded
        if (!$user->relationLoaded('perfil')) {
            $user->load('perfil');
        }

        $slug = $user->perfil->slug ?? 'operador'; // Default to operator

        switch ($slug) {
            case 'admin_local':
                $stats = $dashboardService->getStatsForLocalAdmin($user, $request->all());
                break;
            case 'admin_regional':
            case 'admin_sistema':
                $stats = $dashboardService->getStatsForRegionalAdmin($user);
                break;
            case 'operador':
            default:
                $stats = $dashboardService->getStatsForOperator($user);
                break;
        }

        // Fetch sectors for filter (if Admin Local)
        $sectors = [];
        if ($slug === 'admin_local' && $user->local_id) {
            $sectors = \App\Models\Setor::where('local_id', $user->local_id)
                ->where('active', true)
                ->orderBy('nome')
                ->get();
        }

        return view('dashboard', compact('dailyData', 'nextAppointments', 'stats', 'sectors'));
    }
}
