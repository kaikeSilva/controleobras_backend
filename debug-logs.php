<?php
// Script para debug do sistema de logs

echo "=== DEBUG DO SISTEMA DE LOGS ===\n";

// Verificar se o diretório existe e tem permissões
$logDir = '/var/www/storage/logs';
echo "Diretório de logs: $logDir\n";
echo "Existe: " . (is_dir($logDir) ? 'SIM' : 'NÃO') . "\n";
echo "Gravável: " . (is_writable($logDir) ? 'SIM' : 'NÃO') . "\n";

// Verificar arquivo de log
$logFile = $logDir . '/laravel.log';
echo "Arquivo de log: $logFile\n";
echo "Existe: " . (file_exists($logFile) ? 'SIM' : 'NÃO') . "\n";
echo "Gravável: " . (is_writable($logFile) ? 'SIM' : 'NÃO') . "\n";

if (file_exists($logFile)) {
    echo "Tamanho: " . filesize($logFile) . " bytes\n";
    echo "Última modificação: " . date('Y-m-d H:i:s', filemtime($logFile)) . "\n";
}

echo "\n";

// Verificar permissões detalhadas
echo "=== PERMISSÕES ===\n";
echo "Usuário atual: " . get_current_user() . "\n";
echo "UID: " . getmyuid() . "\n";
echo "GID: " . getmygid() . "\n";

if (file_exists($logFile)) {
    $perms = fileperms($logFile);
    echo "Permissões do arquivo: " . substr(sprintf('%o', $perms), -4) . "\n";
}

$perms = fileperms($logDir);
echo "Permissões do diretório: " . substr(sprintf('%o', $perms), -4) . "\n";

echo "\n";

// Testar escrita direta
echo "=== TESTE DE ESCRITA DIRETA ===\n";
try {
    $testFile = $logDir . '/test.log';
    file_put_contents($testFile, "Teste: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    echo "✅ Escrita direta funcionando\n";
    unlink($testFile);
} catch (Exception $e) {
    echo "❌ Erro na escrita direta: " . $e->getMessage() . "\n";
}

echo "\n";

// Testar Laravel Log
echo "=== TESTE DO LARAVEL LOG ===\n";
try {
    require '/var/www/vendor/autoload.php';
    $app = require_once '/var/www/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Verificar configuração de log
    $logChannel = config('logging.default');
    echo "Canal de log padrão: $logChannel\n";
    
    $logChannels = config('logging.channels');
    echo "Canais disponíveis: " . implode(', ', array_keys($logChannels)) . "\n";
    
    if (isset($logChannels[$logChannel])) {
        echo "Configuração do canal atual:\n";
        print_r($logChannels[$logChannel]);
    }
    
    // Testar log
    \Illuminate\Support\Facades\Log::info('Teste de log do debug script');
    \Illuminate\Support\Facades\Log::error('Teste de erro do debug script');
    
    echo "✅ Laravel Log executado (verifique o arquivo)\n";
    
} catch (Exception $e) {
    echo "❌ Erro no Laravel Log: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";

// Mostrar últimas linhas do log
echo "=== ÚLTIMAS LINHAS DO LOG ===\n";
if (file_exists($logFile) && filesize($logFile) > 0) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -10);
    foreach ($lastLines as $line) {
        echo $line;
    }
} else {
    echo "Arquivo de log vazio ou não existe\n";
}

echo "\n=== FIM DO DEBUG ===\n";
?>