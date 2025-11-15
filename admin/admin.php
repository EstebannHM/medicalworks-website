<?php
declare(strict_types=1);

// Configurar cookies de sesión de forma segura ANTES de session_start
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
  'secure' => is_https(), // En dev (HTTP) será false; en prod (HTTPS) true
  'httponly' => true,
  'samesite' => 'Strict',
]);

session_start();
// Evitar caché en el login para que el botón Atrás no muestre contenido obsoleto
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Headers de seguridad adicionales
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self';");
// HSTS solo si estás bajo HTTPS (no afecta entornos HTTP locales)
if (is_https()) {
  header('Strict-Transport-Security: max-age=31536000');
}

// Cargar variables del entorno
require_once __DIR__ . '/../config/env_loader.php';

// Bloqueo por intentos fallidos (simple en sesión)
const MAX_INTENTOS = 5;
const BLOQUEO_MINUTOS = 10;

function estaBloqueado(): bool {
    if (empty($_SESSION['login_lock_until'])) return false;
    return time() < $_SESSION['login_lock_until'];
}

function registrarFallo(): void {
    $intentos = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['login_attempts'] = $intentos;
    if ($intentos >= MAX_INTENTOS) {
        $_SESSION['login_lock_until'] = time() + BLOQUEO_MINUTOS * 60;
    }
}

function resetBloqueo(): void {
    unset($_SESSION['login_attempts'], $_SESSION['login_lock_until']);
}

// Redirigir si ya está autenticado
if (!empty($_SESSION['admin_auth'])) {
  header('Location: ./dashboard.php');
  exit;
}

// CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $errors[] = 'Solicitud inválida (CSRF).';
    }

    if (estaBloqueado()) {
        $restante = max(0, $_SESSION['login_lock_until'] - time());
        $min = ceil($restante / 60);
        $errors[] = "Demasiados intentos fallidos. Intenta nuevamente en ~{$min} min.";
    }

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $envUser = $_ENV['ADMIN_USER'] ?? null;
  $envPassHash = $_ENV['ADMIN_PASS_HASH'] ?? null;

  if (!$envUser || !$envPassHash) {
        $errors[] = 'Credenciales de administrador no configuradas. Define ADMIN_USER y ADMIN_PASS_HASH en .env.';
    }

    if (!$errors) {
        $okUser = hash_equals($envUser, $username);
        $okPass = false;

    if ($envPassHash) {
      $okPass = password_verify($password, $envPassHash);
    }

    if ($okUser && $okPass) {
      // Regenerar el ID de sesión al autenticar para mitigar fijación de sesión
      session_regenerate_id(true);
      $_SESSION['admin_auth'] = true;
      $_SESSION['admin_user'] = $username;
            resetBloqueo();
            // Rotar CSRF
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
      header('Location: ./dashboard.php', true, 303);
            exit;
    } else {
      registrarFallo();
      // Pequeña espera para mitigar fuerza bruta sin bloquear el servidor
      usleep(300000); // 300ms
      $errors[] = 'Usuario o contraseña incorrectos.';
    }
    }
}

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Acceso administrador</title>
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/admin/admin.css">
  <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>
<body class="admin-login">
  <main class="login-card" role="main" aria-labelledby="titulo-login">
    <h1 id="titulo-login">Ingresar al panel</h1>

    <?php if ($errors): ?>
      <div class="error" role="alert" aria-live="polite">
        <?php foreach ($errors as $e): ?>
          <div><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <form method="post" novalidate autocomplete="off">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

      <div class="field">
        <label for="username">Usuario</label>
        <input id="username" name="username" type="text" autocomplete="username" autocapitalize="none" spellcheck="false" inputmode="text" required>
      </div>

      <div class="field">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <div class="muted">
          <?php if (estaBloqueado()): ?>
            Acceso temporalmente bloqueado por intentos fallidos.
          <?php else: ?>
            Nunca compartas tu contraseña.
          <?php endif; ?>
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="submit" <?= estaBloqueado() ? 'disabled' : '' ?>>Ingresar</button>
        <a class="muted" href="../pages/index.php">Volver al sitio</a>
      </div>
    </form>
  </main>
</body>
</html>