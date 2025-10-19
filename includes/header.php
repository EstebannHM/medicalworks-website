<?php

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
        <img src="../assets/img/carritoCompra.jpg" alt="Carrito de compras" class="cart-icon">
      </a>
      <a class="btn-cotizacion" href="../pages/quote.php">Generar cotización</a>
    </div>

    <button class="navbar-toggle" id="navbarToggle" aria-controls="navbarMenu" aria-expanded="false" aria-label="Abrir menú">☰</button>
  </div>
</header>