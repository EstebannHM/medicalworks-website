<?php
declare(strict_types=1);

// Configurar cookies de sesión seguras ANTES de session_start
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
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'none'; frame-ancestors 'none';");
if (is_https()) {
  header('Strict-Transport-Security: max-age=31536000');
}

// Verificar autenticación
if (empty($_SESSION['admin_auth'])) { 
  header('Location: ./admin.php'); 
  exit; 
}

// Generar token CSRF
$csrfToken = $_SESSION['csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf'] = $csrfToken;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel administrativo</title>
  <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="admin-dashboard">
 esto que es  <noscript>
    <div style="background:#fee2e2;color:#7f1d1d;padding:10px;text-align:center;">
      Para usar el panel administrativo necesitas habilitar JavaScript.
    </div>
  </noscript>
  <header class="admin-nav" role="banner">
    <div class="brand-block" aria-label="Medical Works - Panel Administrativo">
      <div class="brand-icon" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-columns-gap" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
          <path d="M6 1v3H1V1zM1 0a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm14 12v3h-5v-3zm-5-1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zM6 8v7H1V8zM1 7a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zm14-6v7h-5V1zm-5-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z"/>
        </svg>
      </div>
      <div class="brand-text">
        <span class="brand-name">Medical Works</span>
        <span class="brand-sub">Panel Administrativo</span>
      </div>
    </div>
    <div class="nav-rest">
      <h1 class="nav-title">Gestión de Productos</h1>
    </div>
  </header>
  <main class="admin-main">
    <aside class="admin-sidebar" aria-label="Menú principal">
      <nav class="sidebar-section top" aria-label="Navegación principal">
        <ul class="menu-list">
          <li>
            <a href="#productos" class="menu-item active" data-section="productos">
              <span class="icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                  <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
                </svg>
              </span>
              <span class="text-group">
                <span class="label">Productos</span>
                <span class="sublabel">Gestionar catálogo</span>
              </span>
            </a>
          </li>
          <li>
            <a href="#proveedores" class="menu-item" data-section="proveedores">
              <span class="icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                  <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                </svg>
              </span>
              <span class="text-group">
                <span class="label">Proveedores</span>
                <span class="sublabel">Gestionar proveedores</span>
              </span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="sidebar-spacer" aria-hidden="true"></div>
      <div class="sidebar-section bottom" aria-label="Cuenta">
        <div class="admin-user-card">
          <div class="user-avatar" aria-hidden="true">AD</div>
          <div class="user-info">
            <span class="user-name">Admin</span>
            <span class="user-mail">admin@medicalworks.com</span>
          </div>
        </div>
        <form method="post" action="logout.php" class="logout-form">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
          <button type="submit" class="logout-btn" title="Cerrar sesión">
            <span class="icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
              </svg>
            </span>
            <span class="label">Cerrar sesión</span>
          </button>
        </form>
      </div>
    </aside>
    <section class="content-area">
      <div class="kpi-cards" aria-label="Resumen rápido">
        <div class="kpi-card" aria-labelledby="kpi-productos-label">
          <div class="kpi-icon gradient-purple" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
              <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
            </svg>
          </div>
          <p id="kpi-productos-label" class="kpi-label">TOTAL PRODUCTOS</p>
          <p class="kpi-value">48</p>
        </div>
        <div class="kpi-card" aria-labelledby="kpi-proveedores-label">
          <div class="kpi-icon gradient-green" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
              <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
            </svg>
          </div>
          <p id="kpi-proveedores-label" class="kpi-label">PROVEEDORES</p>
          <p class="kpi-value" id="kpiProvidersValue" aria-live="polite">--</p>
        </div>
        <div class="kpi-card" aria-labelledby="kpi-cotizaciones-label">
          <div class="kpi-icon gradient-purple" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
              <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l1.25 5h8.22l1.25-5zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
            </svg>
          </div>
          <p id="kpi-cotizaciones-label" class="kpi-label">COTIZACIONES</p>
          <p class="kpi-value">124</p>
        </div>
      </div>
      <!-- Barra de búsqueda y filtros + Tabla de productos -->
      <section id="adminProducts" class="admin-products">
        <div class="products-toolbar" aria-label="Búsqueda y filtros">
          <div class="toolbar-left">
            <div class="search-box">
              <label for="productSearch" class="visually-hidden">Buscar productos</label>
              <input type="text" id="productSearch" placeholder="Buscar productos..." autocomplete="off" aria-label="Buscar productos">
            </div>
            <div class="filters-group" aria-label="Filtros">
              <button type="button" class="btn-filter" id="btnFilterCategory" title="Filtrar por categoría" aria-label="Filtrar por categoría">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                  <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                </svg>
                <span>Todas las categorías</span>
              </button>
              <button type="button" class="btn-filter" id="btnFilterProvider" title="Filtrar por proveedor" aria-label="Filtrar por proveedor">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                  <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                </svg>
                <span>Todos los proveedores</span>
              </button>
              <button type="button" class="btn-filter" id="btnFilterStatus" title="Filtrar por estado" aria-label="Filtrar por estado">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                  <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z"/>
                </svg>
                <span>Todos los estados</span>
              </button>
            </div>
          </div>
          <div class="toolbar-right">
            <button type="button" class="btn-create-product" id="btnCreateProduct" title="Nuevo Producto" aria-label="Nuevo Producto">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
              </svg>
              <span class="btn-label">Nuevo Producto</span>
            </button>
          </div>
        </div>
        <div class="table-wrapper" id="tableMount"></div>
        <div class="table-footer">
          <div id="tablePageInfo" class="page-info" aria-live="polite"></div>
          <div id="tablePagination" class="pagination"></div>
        </div>
      </section>
    </section>
  </main>
  <script src="../assets/js/dashboard.js" defer></script>
</body>
</html>
