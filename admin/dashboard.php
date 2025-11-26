<?php

declare(strict_types=1);

// Configurar cookies de sesión seguras ANTES de session_start
ini_set('session.use_strict_mode', '1');

function is_https(): bool
{
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
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; object-src 'none'; base-uri 'none'; frame-ancestors 'none';");
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
  <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
  <link rel="stylesheet" href="../assets/css/admin/modal-product.css">
  <link rel="stylesheet" href="/assets/css/admin/modal-provider.css">
  <link rel="icon" href="../assets/img/logo.jpeg" type="image/jpeg">
</head>

<body class="admin-dashboard">
  <noscript>
    <div style="background:#fee2e2;color:#7f1d1d;padding:10px;text-align:center;">
      Para usar el panel administrativo necesitas habilitar JavaScript.
    </div>
  </noscript>
  <header class="admin-nav" role="banner">
    <div class="brand-block" aria-label="Medical Works - Panel Administrativo">
      <div class="brand-icon" aria-hidden="true">
        <svg width="22" height="22" fill="currentColor" class="bi bi-columns-gap" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
          <path d="M6 1v3H1V1zM1 0a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm14 12v3h-5v-3zm-5-1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zM6 8v7H1V8zM1 7a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1zm14-6v7h-5V1zm-5-1a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z" />
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
                <svg width="18" height="18" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                  <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
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
                <svg width="18" height="18" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                  <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                </svg>
              </span>
              <span class="text-group">
                <span class="label">Proveedores</span>
                <span class="sublabel">Gestionar proveedores</span>
              </span>
            </a>
          </li>
          <li>
            <a href="#categorias" class="menu-item active" data-section="categorias">
              <span class="icon" aria-hidden="true">
                <svg width="16" height="16" fill="currentColor" class="bi bi-tags" viewBox="0 0 16 16">
                  <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z" />
                  <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1z" />
                </svg>
              </span>
              <span class="text-group">
                <span class="label">Categorías</span>
                <span class="sublabel">Gestionar categorías</span>
              </span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="sidebar-spacer" aria-hidden="true"></div>
      <div class="sidebar-section bottom" aria-label="Cuenta">
        <form method="post" action="logout.php" class="logout-form">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
          <button type="submit" class="logout-btn" title="Cerrar sesión">
            <span class="icon" aria-hidden="true">
              <svg width="17" height="17" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
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
            <svg width="20" height="20" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
              <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z" />
            </svg>
          </div>
          <p id="kpi-productos-label" class="kpi-label">TOTAL PRODUCTOS</p>
          <p class="kpi-value"></p>
        </div>
        <div class="kpi-card" aria-labelledby="kpi-proveedores-label">
          <div class="kpi-icon gradient-green" aria-hidden="true">
            <svg width="20" height="20" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
              <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
            </svg>
          </div>
          <p id="kpi-proveedores-label" class="kpi-label">PROVEEDORES</p>
          <p class="kpi-value" id="kpiProvidersValue" aria-live="polite"></p>
        </div>
        <div class="kpi-card" aria-labelledby="kpi-cotizaciones-label">
          <div class="kpi-icon gradient-purple" aria-hidden="true">
            <svg width="20" height="20" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
              <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l1.25 5h8.22l1.25-5zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0" />
            </svg>
          </div>
          <p id="kpi-cotizaciones-label" class="kpi-label">COTIZACIONES</p>
          <p class="kpi-value"></p>
        </div>
      </div>
      <!-- Sección Principal con Toolbars Dinámicas -->
      <section id="adminContent" class="admin-content">
        
        <!-- Toolbar para PRODUCTOS -->
        <div class="products-toolbar" id="toolbarProductos" aria-label="Búsqueda y filtros de productos">
          <div class="toolbar-left">
            <div class="search-box">
              <label for="productSearch" class="visually-hidden">Buscar productos</label>
              <input type="text" id="productSearch" placeholder="Buscar productos..." autocomplete="off" aria-label="Buscar productos">
            </div>
            <div class="filters-group" aria-label="Filtros">
              <div class="dropdown-wrapper">
                <button class="dropdown-toggle" id="categoryDropdown">
                  <svg width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                  </svg>
                  <span class="dropdown-text">Todas las categorías</span>
                </button>
                <div class="dropdown-menu" id="categoryDropdownMenu">
                  <button class="dropdown-item active" data-category="all">Todas las categorías</button>
                  <div id="categoriesContainer"></div>
                </div>
              </div>
              <div class="dropdown-wrapper">
                <button class="dropdown-toggle" id="providerDropdown">
                  <svg width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                  </svg>
                  <span class="dropdown-text">Todos los proveedores</span>
                </button>
                <div class="dropdown-menu" id="providerDropdownMenu">
                  <button class="dropdown-item active" data-provider="all">Todos los proveedores</button>
                  <div id="providersContainer"></div>
                </div>
              </div>
              <div class="filter-section">
                <div class="dropdown-wrapper">
                  <button class="dropdown-toggle" id="statusDropdown">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                      <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                    </svg>
                    <span class="dropdown-text">Todos los estados</span>
                  </button>
                  <div class="dropdown-menu" id="statusDropdownMenu">
                    <button class="dropdown-item active" data-status="all">Todos los estados</button>
                    <button class="dropdown-item" data-status="active">Activo</button>
                    <button class="dropdown-item" data-status="inactive">Inactivo</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="toolbar-right">
            <button type="button" class="btn-create-product" id="btnCreateProduct" title="Nuevo Producto" aria-label="Nuevo Producto">
              <svg width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
              </svg>
              <span class="btn-label">Nuevo Producto</span>
            </button>
          </div>
        </div>

        <!-- Toolbar para CATEGORÍAS -->
        <div class="products-toolbar" id="toolbarCategorias" style="display: none;" aria-label="Búsqueda y filtros de categorías">
          <div class="toolbar-left">
            <div class="search-box">
              <label for="categorySearch" class="visually-hidden">Buscar categorías</label>
              <input type="text" id="categorySearch" placeholder="Buscar categorías..." autocomplete="off" aria-label="Buscar categorías">
            </div>
          </div>
          <div class="toolbar-right">
            <button type="button" class="btn-create-product" id="btnCreateCategory" title="Nueva Categoría" aria-label="Nueva Categoría">
              <svg width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
              </svg>
              <span class="btn-label">Nueva Categoría</span>
            </button>
          </div>
        </div>

        <!-- Toolbar para PROVEEDORES -->
        <div class="products-toolbar" id="toolbarProveedores" style="display: none;" aria-label="Búsqueda y filtros de proveedores">
          <div class="toolbar-left">
            <div class="search-box">
              <label for="providerSearch" class="visually-hidden">Buscar proveedores</label>
              <input type="text" id="providerSearch" placeholder="Buscar proveedores..." autocomplete="off" aria-label="Buscar proveedores">
            </div>
            <div class="filters-group" aria-label="Filtros">
              <div class="filter-section">
                <div class="dropdown-wrapper">
                  <button class="dropdown-toggle" id="providerStatusDropdown">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16" aria-hidden="true">
                      <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                    </svg>
                    <span class="dropdown-text">Todos los estados</span>
                  </button>
                  <div class="dropdown-menu" id="providerStatusDropdownMenu">
                    <button class="dropdown-item active" data-status="all">Todos los estados</button>
                    <button class="dropdown-item" data-status="active">Activo</button>
                    <button class="dropdown-item" data-status="inactive">Inactivo</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="toolbar-right">
            <button type="button" class="btn-create-product" id="btnCreateProvider" title="Nuevo Proveedor" aria-label="Nuevo Proveedor">
              <svg width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16" aria-hidden="true">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
              </svg>
              <span class="btn-label">Nuevo Proveedor</span>
            </button>
          </div>
        </div>

        <!-- Tabla Universal (se reutiliza para todo) -->
        <div class="table-wrapper" id="tableMount"></div>
        <div class="table-footer">
          <div id="tablePageInfo" class="page-info" aria-live="polite"></div>
          <div id="tablePagination" class="pagination"></div>
        </div>
      </section>
    </section>
  </main>

  <!-- Modal Agregar Producto -->
  <div id="modalAddProduct" class="modal-overlay" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-container">
      <div class="modal-header">
        <h2 id="modalTitle">Crear Nuevo Producto</h2>
        <p id="modalSubtitle" class="modal-subtitle">Completa los campos para agregar un producto</p>
        <button type="button" class="modal-close" aria-label="Cerrar modal">
          <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
          </svg>
        </button>
      </div>

      <form id="formAddProduct" class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label for="productTitle">Título del Producto <span class="required">*</span></label>
            <input type="text" id="productTitle" name="name" placeholder="Ej. Estetoscopio Profesional" required maxlength="255">
          </div>

          <div class="form-group">
            <label for="productSku">Código SKU <span class="required">*</span></label>
            <input type="text" id="productSku" name="sku" placeholder="Ej. MED-001" required maxlength="50">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="productCategory">Categoría <span class="required">*</span></label>
            <select id="productCategory" name="id_category" required>
              <option value="">Seleccionar categoría</option>
            </select>
          </div>

          <div class="form-group">
            <label for="productProvider">Proveedor <span class="required">*</span></label>
            <select id="productProvider" name="id_provider" required>
              <option value="">Seleccionar proveedor</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="productDescription">Descripción <span class="required">*</span></label>
          <textarea id="productDescription" name="description" rows="4" placeholder="Describa las características y beneficios del producto..." required maxlength="1000"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="productImage">Imagen del Producto <span class="required">*</span></label>
            <div class="file-upload-wrapper">
              <input type="file" id="productImage" name="image" accept="image/jpeg,image/png,image/jpg" required>
              <div class="file-upload-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                  <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                </svg>
                <span class="file-label">Subir Imagen</span>
                <span class="file-hint">JPG, PNG (Máx. 5MB)</span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="productPdf">Ficha Técnica (PDF)</label>
            <div class="file-upload-wrapper pdf-upload">
              <input type="file" id="productPdf" name="pdf" accept="application/pdf">
              <div class="file-upload-content">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                  <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029z" />
                </svg>
                <span class="file-label">Subir PDF</span>
                <span class="file-hint">Ficha técnica (Máx. 10MB)</span>
              </div>
            </div>

            <!-- Preview del PDF -->
            <div class="datasheet-preview-container" id="pdfPreviewContainer">
              <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029z" />
              </svg>
              <span class="datasheet-file-name" id="pdfFileName"></span>
              <button type="button" class="remove-datasheet" id="removePdf" title="Eliminar ficha técnica">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="form-group form-group-status">
          <label>Estado <span class="required">*</span></label>
          <div class="status-toggle">
            <input type="checkbox" id="productStatus" name="status" class="status-checkbox" checked>
            <label for="productStatus" class="status-toggle-label"></label>
            <span class="status-text">Activo</span>
          </div>
        </div>

        <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
          <label>Vista Previa</label>
          <div class="image-preview">
            <img id="imagePreview" src="" alt="Vista previa">
            <button type="button" class="remove-preview" aria-label="Eliminar imagen">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
              </svg>
            </button>
          </div>
        </div>

        <div id="formError" class="form-error" style="display: none;"></div>
      </form>

      <div class="modal-footer">
        <button type="button" class="btn-preview" id="btnPreview">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
          </svg>
          Previsualizar
        </button>
        <div class="modal-actions">
          <button type="button" class="btn-cancel" id="btnCancelProduct">Cancelar</button>
          <button type="submit" form="formAddProduct" class="btn-save" id="btnSaveProduct">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z" />
            </svg>
            Guardar Producto
          </button>
        </div>
      </div>
    </div>
  </div>
<!-- Modal Agregar/Editar Proveedor -->
<div id="modalAddProvider" class="modal-overlay" role="dialog" aria-labelledby="modalProviderTitle" aria-hidden="true">
  <div class="modal-container">
    <div class="modal-header">
      <h2 id="modalProviderTitle">Crear Nuevo Proveedor</h2>
      <p id="modalProviderSubtitle" class="modal-subtitle">Completa los campos para agregar un proveedor</p>
      <button type="button" class="modal-close" aria-label="Cerrar modal" id="btnCloseProviderModal">
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
          <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
        </svg>
      </button>
    </div>

    <form id="formAddProvider" class="modal-body" enctype="multipart/form-data">
      <div class="form-group">
        <label for="providerName">Nombre del Proveedor <span class="required">*</span></label>
        <input type="text" id="providerName" name="name" placeholder="Ej. Medline Industries" required maxlength="150">
      </div>

      <div class="form-group">
        <label for="providerWebsite">Sitio Web</label>
        <input type="url" id="providerWebsite" name="website_url" placeholder="https://ejemplo.com" maxlength="500">
      </div>

      <div class="form-group">
        <label for="providerDescription">Descripción <span class="required">*</span></label>
        <textarea id="providerDescription" name="description" rows="4" placeholder="Describa al proveedor, sus productos principales y características..." required maxlength="1000"></textarea>
      </div>

      <div class="form-group">
        <label for="providerImage">Imagen/Logo del Proveedor <span class="required">*</span></label>
        <div class="file-upload-wrapper">
          <input type="file" id="providerImage" name="image" accept="image/jpeg,image/png,image/jpg,image/avif,image/webp" required>
          <div class="file-upload-content">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
              <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
              <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
            </svg>
            <span class="file-label">Subir Imagen</span>
            <span class="file-hint">JPG, PNG (Máx. 5MB)</span>
          </div>
        </div>
      </div>

      <div class="form-group form-group-status">
        <label>Estado <span class="required">*</span></label>
        <div class="status-toggle">
          <input type="checkbox" id="providerStatus" name="status" class="status-checkbox" checked>
          <label for="providerStatus" class="status-toggle-label"></label>
          <span class="status-text">Activo</span>
        </div>
      </div>

      <div id="providerImagePreviewContainer" class="image-preview-container" style="display: none;">
        <label>Vista Previa</label>
        <div class="image-preview">
          <img id="providerImagePreview" src="" alt="Vista previa">
          <button type="button" class="remove-preview" id="btnRemoveProviderPreview" aria-label="Eliminar imagen">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
            </svg>
          </button>
        </div>
      </div>

      <div id="formProviderError" class="form-error" style="display: none;"></div>
    </form>

    <div class="modal-footer">
      <div class="modal-actions">
        <button type="button" class="btn-cancel" id="btnCancelProvider">Cancelar</button>
        <button type="submit" form="formAddProvider" class="btn-save" id="btnSaveProvider">
          <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z" />
          </svg>
          Guardar Proveedor
        </button>
      </div>
    </div>
  </div>
</div>


  <script src="../assets/js/admin/dashboard-common.js" defer></script>
  <script src="../assets/js/admin/dashboard-products.js" defer></script>
  <script src="../assets/js/admin/dashboard-categories.js" defer></script>
  <script src="../assets/js/admin/dashboard-providers.js" defer></script>
  <script src="../assets/js/admin/dashboard.js" defer></script>
  <script src="../assets/js/modal-product.js" defer></script>
  <script src="../assets/js/modal-provider.js"></script>
</body>

</html>