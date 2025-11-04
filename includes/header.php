<?php
require_once __DIR__ . '/cart-helper.php';
$cartItems = getCartItemsCount();
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<header class="navbar" role="banner">
  <div class="navbar-container">
    <a href="../pages/index.php" class="navbar-logo">
      <img src="../assets/img/logo.jpeg" alt="Medical Works - Logo">
    </a>

    <nav class="navbar-menu" id="navbarMenu" role="navigation" aria-label="Menú principal" aria-hidden="true">
      <ul>
        <li><a href="../pages/index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Inicio</a></li>
        <li><a href="../pages/about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">Quiénes somos</a></li>
        <li><a href="../pages/catalog.php" class="<?= $currentPage === 'catalog.php' ? 'active' : '' ?>">Productos</a></li>
        <li><a href="../pages/contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Contacto</a></li>
      </ul>
    </nav>

    <div class="navbar-actions">
      <a class="cart" href="../pages/cart.php" aria-label="Ver carrito">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" alt="Carrito de compras">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
        </svg>
        <span class="cart-badge">
          <?= $cartItems ?>
        </span>
      </a>
      <a class="btn-cotizacion" href="../pages/quote.php">Generar cotización</a>
    </div>

    <button class="navbar-toggle" id="navbarToggle" aria-controls="navbarMenu" aria-expanded="false" aria-label="Abrir menú">☰</button>
  </div>
</header>