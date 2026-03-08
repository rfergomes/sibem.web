<?php
// Script Temporário para Limpeza de Cache no cPanel via Navegador
// Execute acessando: https://sibem.top/limpar.php

$repo_dir = __DIR__;
chdir($repo_dir);

echo "<h1>Limpando Caches do Laravel...</h1>";

echo "<div style='background:#1e1e1e; color:#0f0; padding:15px; border-radius:5px; font-family:monospace;'>";

// Roda os comandos
echo "<b>1. Optimize Clear (Apaga Cache de Rotas, Views, Config e App):</b><br>";
echo "<pre>" . shell_exec('php artisan optimize:clear 2>&1') . "</pre><hr>";

echo "</div>";

echo "<br><br><p>Se você viu as mensagens verdes de SUCESSO acima, as rotas novas do Webhook do GitHub já estão ativas!</p>";
echo "<p>Agora você já pode ir no GitHub e testar o Webhook chamando: <code>/api/deploy_webhook_secreto123</code></p>";
echo "<b style='color:red'>⚠️ IMPORTANTE:</b> Recomendo apagar este arquivo (limpar.php) da sua hospedagem quando terminar os testes por segurança.";
?>