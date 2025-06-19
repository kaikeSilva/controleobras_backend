<?php
// Script para debug da conexão com banco de dados

echo "=== DEBUG DE CONEXÃO COM BANCO ===\n";

// Variáveis de ambiente
$host = $_ENV['DB_HOST'] ?? 'db';
$port = $_ENV['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? 'controleobras_prod';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? 'sua_senha_super_segura_aqui_2024!';

echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? 'VAZIO' : 'DEFINIDO (' . strlen($password) . ' chars)') . "\n";
echo "\n";

// Teste 1: Conectar ao MySQL sem especificar database
echo "=== TESTE 1: Conexão básica ao MySQL ===\n";
try {
    $dsn = "mysql:host=$host;port=$port";
    echo "DSN: $dsn\n";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Conexão básica estabelecida!\n";
    
    // Verificar versão do MySQL
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "Versão do MySQL: $version\n";
    
    $pdo = null;
} catch (Exception $e) {
    echo "❌ Erro na conexão básica: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Teste 2: Criar database se não existir
echo "=== TESTE 2: Criar database ===\n";
try {
    $dsn = "mysql:host=$host;port=$port";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    echo "✅ Database '$database' criada/verificada!\n";
    
    $pdo = null;
} catch (Exception $e) {
    echo "❌ Erro ao criar database: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Teste 3: Conectar ao database específico
echo "=== TESTE 3: Conexão ao database específico ===\n";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database";
    echo "DSN: $dsn\n";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Conexão ao database estabelecida!\n";
    
    // Teste simples
    $result = $pdo->query('SELECT 1 as test')->fetch();
    echo "Teste SQL: " . $result['test'] . "\n";
    
    $pdo = null;
} catch (Exception $e) {
    echo "❌ Erro na conexão ao database: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
echo "🎉 Todos os testes passaram! Conexão está funcionando.\n";
?>