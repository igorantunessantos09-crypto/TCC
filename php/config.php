<?php
// php/config.php
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

$appEnv = env('APP_ENV', 'production');

// Cookies de sessão mais seguros - precisa vir ANTES do session_start()
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    // 'secure' fica true automaticamente quando o site já estiver em HTTPS
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

$host = env('DB_HOST', 'localhost');
$dbname = env('DB_NAME', 'tcc');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', '');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Nunca mostrar detalhes internos do banco para o usuário final
    error_log('Erro de conexão com o banco: ' . $e->getMessage());
    if ($appEnv === 'development') {
        die('Erro de conexão: ' . $e->getMessage());
    }
    die('Não foi possível conectar ao sistema no momento. Tente novamente em instantes.');
}

require_once __DIR__ . '/csrf.php';

// Funções auxiliares
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

function isAdmin() {
    return isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>