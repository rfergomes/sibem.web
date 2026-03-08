<?php
// Arquivo: webhook.php
// Esse arquivo fica na pasta public/ e recebe o PUSH direto do GitHub

// Vai para o diretório raiz do projeto (uma pasta pra trás do public)
$repo_dir = dirname(__DIR__);
chdir($repo_dir);

// Executa o git pull puxando da branch master
$output = shell_exec('/usr/bin/git pull origin master 2>&1');

// Se o comando acima não encontrar o 'git', tentaremos apenas 'git pull...'
if (!$output) {
    $output = shell_exec('git pull origin master 2>&1');
}

// Retorna pro GitHub dizendo que deu tudo certo e mostra o log do terminal
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Deploy executado com sucesso via script standalone.',
    'log' => $output
]);
?>