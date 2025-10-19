document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('navbarToggle');
  const menu = document.getElementById('navbarMenu');

  
  if (!toggle || !menu) return;

  const closeMenu = () => {
    menu.setAttribute('aria-hidden', 'true');
    toggle.setAttribute('aria-expanded', 'false');
    menu.classList.remove('open');
  };

  const openMenu = () => {
    menu.setAttribute('aria-hidden', 'false');
    toggle.setAttribute('aria-expanded', 'true');
    menu.classList.add('open');
  };

  toggle.addEventListener('click', function () {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    if (isOpen) closeMenu(); else openMenu();
  });

  
  window.addEventListener('resize', function () {
    if (window.innerWidth > 768) {
      closeMenu();
    }
  });

 
  menu.addEventListener('click', function (e) {
    if (e.target.tagName === 'A' && window.innerWidth <= 768) {
      closeMenu();
    }
  });
});