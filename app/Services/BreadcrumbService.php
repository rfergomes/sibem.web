<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;

class BreadcrumbService
{
    protected $mapping = [
        'dashboard' => 'Dashboard',
        'inventarios' => 'Inventários',
        'bens' => 'Patrimônio',
        'users' => 'Usuários',
        'admin' => 'Adm',
        'locais' => 'Locais',
        'igrejas' => 'Igrejas',
        'setores' => 'Setores',
        'dependencias' => 'Dependências',
        'profile' => 'Meu Perfil',
        'create' => 'Novo Cadastro',
        'edit' => 'Edição',
        'show' => 'Detalhes',
        'solicitacoes' => 'Solicitações de Acesso',
        'bens-import' => 'Importação Excel',
        'conferencia' => 'Conferência',
    ];

    public function generate()
    {
        $segments = Request::segments();
        $breadcrumbs = [];
        $url = '';

        // Always start with Dashboard if we are authenticated, but if the first segment is dashboard loop will handle it (or we can skip/dedupe)
        // Actually, let's just parse segments.

        foreach ($segments as $key => $segment) {
            $url .= '/' . $segment;

            // Skip numeric IDs usually, unless we want to show them or fetch name
            if (is_numeric($segment)) {
                // Optionally we could fetch the model name based on previous segment, but for now let's skip or show generic
                // Let's just ignore IDs in the visual text or keep them as "ID: 123" if needed.
                // For this request: "edit: nome da página/edit". So users/1/edit -> Usuários > Edição.
                // We typically skip the ID in the visual breadcrumb trail unless it's the leaf.
                // But if it's users/1/edit, we want "Usuários" -> "Edição". The "1" is implicit context.
                // Let's skip numeric segments for the *text*, but keep them for the *url*.
                continue;
            }

            $title = $this->mapping[$segment] ?? ucfirst($segment);

            $breadcrumbs[] = [
                'title' => $title,
                'url' => url($url),
                'is_current' => ($key == count($segments) - 1)
            ];
        }

        // If generic "dashboard" is not in segments (e.g. root /), we might want to prepend it? 
        // But usually dashboard is /dashboard.

        return $breadcrumbs;
    }
}
