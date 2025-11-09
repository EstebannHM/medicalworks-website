<?php
declare(strict_types=1);

// Configurar cookies antes de iniciar sesión para respetar flags en destrucción
ini_set('session.use_strict_mode', '1');

function is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return true;
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') return true;
    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') return true;
    return false;
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => is_https(),
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

// Headers mínimos (no hace falta CSP aquí pero añadimos anti-cache y clickjacking)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
if (is_https()) {
    header('Strict-Transport-Security: max-age=31536000');
}

// Solo aceptar petición POST para logout
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./dashboard.php');
    exit;
}

// Validar token CSRF
if (!isset($_POST['csrf']) || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    // Token inválido, redirigir al dashboard
    header('Location: ./dashboard.php');
    exit;
}

// Limpiar datos de sesión
$_SESSION = [];

// Borrar cookie de sesión si existe
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destruir sesión
session_destroy();

header('Location: ./admin.php');
exit;