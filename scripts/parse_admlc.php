<?php

$sql = file_get_contents('d:\xampp\htdocs\sibem.web\admlc.sql');

// Match all value groups: (id, 'name', 'social', 'cnpj', 'city', 'uf', status, regional)
// Note: handling NULLs and empty strings
preg_match_all('/\(\s*(\d+)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(?:\'([^\']*)\'|NULL)\s*,\s*(\d+|NULL)\s*,\s*(\d+|NULL)\s*\)/', $sql, $matches, PREG_SET_ORDER);

$output = [];
$cutoffId = 406;

foreach ($matches as $match) {
    $id = $match[1];

    // Skip if existing or special case 0 (though 0 is usually handled separately)
    if ($id <= $cutoffId && $id != 0)
        continue;
    if ($id == 0)
        continue; // Skip 0 as well if we want to stick to > 406, but wait, ID 0 is mapped to 100 in seeder? 
    // Actually ID 0 is "NÃO DEFINIDA" in SQL. In seeder we have logic for "NÃO DEFINIDO" at ID 1.
    // Let's strictly follow "missing data" -> IDs > 406.

    $nome = $match[2] ?? '';
    // $social = $match[3]; // Not used in seeder array structure shown in previous turn, but wait, seeder DOES parse it: 'razao_social' => $local['razao_social']
    $razao_social = $match[3] ?? '';
    $cnpj = $match[4] ?? '';
    $cidade = $match[5] ?? '';
    $uf = $match[6] ?? '';
    $id_status = $match[7] ?? 0;
    $id_admrg = $match[8] ?? 0;

    $output[] = [
        'id' => (int) $id,
        'nome' => $nome,
        'razao_social' => $razao_social,
        'cnpj' => $cnpj,
        'cidade' => $cidade,
        'uf' => $uf,
        'id_status' => (int) $id_status,
        'id_admrg' => (int) $id_admrg,
    ];
}

// Output content
$content = "<?php\n\n\$locais_part2 = [\n";
foreach ($output as $local) {
    if (empty($local['nome']))
        continue; // Skip empty rows

    $nome = addslashes($local['nome']);
    $razao_social = addslashes($local['razao_social']);
    $cnpj = addslashes($local['cnpj']);
    $cidade = addslashes($local['cidade']);

    $content .= "    ['id' => {$local['id']}, 'nome' => '$nome', 'razao_social' => '$razao_social', 'cnpj' => '$cnpj', 'cidade' => '$cidade', 'uf' => '{$local['uf']}', 'id_status' => {$local['id_status']}, 'id_admrg' => {$local['id_admrg']}],\n";
}
$content .= "];\n";

file_put_contents('d:\xampp\htdocs\sibem.web\scripts\locais_part2.php', $content);
echo "File created successfully.\n";
