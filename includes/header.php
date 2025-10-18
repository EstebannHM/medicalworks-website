<header class="navbar" role="banner">
  <div class="navbar-container">
    <!-- Logo -->
    <a href="../pages/index.php" class="navbar-logo">
      <img src="../assets/img/logo.jpeg" alt="Medical Works - Logo">
    </a>

    <!-- Menú -->
    <nav class="navbar-menu" id="navbarMenu" role="navigation" aria-label="Menú principal" aria-hidden="true">
      <ul>
        <li><a href="../pages/index.php">Inicio</a></li>
        <li><a href="../pages/about.php">Quiénes somos</a></li>
        <li><a href="../pages/catalog.php">Productos</a></li>
        <li><a href="../pages/contact.php">Contacto</a></li>
      </ul>
    </nav>

    <!-- Botón y carrito -->
    <div class="navbar-actions">
      <a class="cart" href="../pages/cart.php" aria-label="Ver carrito">🛒</a>
      <a class="btn-cotizacion" href="../pages/quote.php">Generar cotización</a>
    </div>

    <!-- Botón hamburguesa para móviles -->
    <button class="navbar-toggle" id="navbarToggle" aria-controls="navbarMenu" aria-expanded="false" aria-label="Abrir menú">☰</button>
  </div>
</header>

<!-- Script para menú móvil -->
<script>
  (function() {
    const toggle = document.getElementById('navbarToggle');
    const menu = document.getElementById('navbarMenu');

    function closeMenu() {
      menu.setAttribute('aria-hidden', 'true');
      toggle.setAttribute('aria-expanded', 'false');
      menu.classList.remove('open');
    }
    function openMenu() {
      menu.setAttribute('aria-hidden', 'false');
      toggle.setAttribute('aria-expanded', 'true');
      menu.classList.add('open');
    }

    toggle.addEventListener('click', function() {
      const isOpen = toggle.getAttribute('aria-expanded') === 'true';
      if (isOpen) closeMenu(); else openMenu();
    });

    // Cerrar menú si se redimensiona a escritorio
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        closeMenu();
        menu.style.display = ''; // dejar que CSS lo gestione
      }
    });

    // Cerrar menú al hacer click en un enlace (móvil)
    menu.addEventListener('click', function(e) {
      if (e.target.tagName === 'A' && window.innerWidth <= 768) {
        closeMenu();
      }
    });
  })();
</script>