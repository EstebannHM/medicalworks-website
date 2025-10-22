const swiper = new Swiper(".hero-swiper", {
  loop: true,

  autoplay: {
    delay: 5000, // Cambia cada 5 segundos
    disableOnInteraction: false,
  },

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },

  speed: 600,

  keyboard: {
    enabled: true,
  },
  touchRatio: 1,
});
