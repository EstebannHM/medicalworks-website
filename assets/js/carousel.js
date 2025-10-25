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

const providersSwiper = new Swiper(".providers-swiper", {
  loop: true,

  autoplay: {
    delay: 5000, // Cambia cada 5 segundos
    disableOnInteraction: false,
  },

  pagination: {
    el: ".providers-swiper .swiper-pagination",
    clickable: true,
  },

  speed: 600,

  keyboard: {
    enabled: true,
  },
  touchRatio: 1,

  breakpoints: {
    320: {
      slidesPerView: 3,
      spaceBetween: 15,
    },
    768: {
      slidesPerView: 4,
      spaceBetween: 25,
    },
    1024: {
      slidesPerView: 5,
      spaceBetween: 30,
    },
  },
});

