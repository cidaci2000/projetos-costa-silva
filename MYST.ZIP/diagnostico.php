<?php
// diagnostico.php
// Diagnóstico do sistema

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Diagnóstico LuxDrive</h1>";

// 1. Verificar configuração
echo "<h2>1. Configuração</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "</pre>";

// 2. Verificar arquivos
echo "<h2>2. Arquivos do Sistema</h2>";
echo "<pre>";
$files = [
    'config/database.php',
    'api/api_contato.php',
    'api/api_mensagens.php',
    'api/api_auth.php',
    'api/api_carros.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✅ " . $file . " (" . filesize($path) . " bytes)\n";
    } else {
        echo "❌ " . $file . " - NÃO ENCONTRADO\n";
    }
}
echo "</pre>";

// 3. Testar conexão com banco
echo "<h2>3. Conexão com Banco</h2>";
try {
    require_once 'config/database.php';
    $pdo = getConnection();
    echo "<p style='color:green;'>✅ Conexão estabelecida</p>";
    
    // Verificar tabelas
    echo "<h3>Tabelas:</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    echo "</pre>";
    
    // Verificar estrutura da tabela mensagens
    if (in_array('mensagens', $tables)) {
        echo "<h3>Estrutura da tabela 'mensagens':</h3>";
        $columns = $pdo->query("DESCRIBE mensagens")->fetchAll();
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Chave</th><th>Padrão</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar mensagens
        $count = $pdo->query("SELECT COUNT(*) FROM mensagens")->fetchColumn();
        echo "<p>Total de mensagens: <strong>$count</strong></p>";
    } else {
        echo "<p style='color:red;'>❌ Tabela 'mensagens' não encontrada!</p>";
        echo "<p>Execute o schema.sql para criar as tabelas.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

// 4. Testar API
echo "<h2>4. Teste da API de Contato</h2>";
echo "<pre>";
$ch = curl_init('http://' . $_SERVER['HTTP_HOST'] . '/luxdrive/api/api_contato.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'nome' => 'Diagnóstico',
    'email' => 'diag@teste.com',
    'telefone' => '(11) 99999-9999',
    'assunto' => 'Teste',
    'mensagem' => 'Teste do diagnóstico'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Resposta: $response\n";

if ($httpCode === 200) {
    $json = json_decode($response, true);
    if ($json && isset($json['success']) && $json['success']) {
        echo "✅ API funcionando!\n";
    } else {
        echo "⚠️ API respondeu mas com erro: " . ($json['error'] ?? 'Erro desconhecido') . "\n";
    }
} else {
    echo "❌ API retornou erro HTTP $httpCode\n";
}
echo "</pre>";

// 5. Permissões
echo "<h2>5. Permissões</h2>";
echo "<pre>";
$dirs = ['logs', 'api', 'config'];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        echo "📁 $dir: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
    } else {
        echo "❌ $dir: não encontrado\n";
    }
}
echo "</pre>";

echo "<hr>";
echo "<p><strong>Instruções:</strong></p>";
echo "<ol>";
echo "<li>Verifique se todas as tabelas existem</li>";
echo "<li>Execute o schema.sql se necessário</li>";
echo "<li>Verifique os logs em logs/contato.log</li>";
echo "<li>Teste o formulário em teste_contato.html</li>";
echo "</ol>";
?>