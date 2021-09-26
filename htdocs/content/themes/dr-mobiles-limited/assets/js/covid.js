import Swiper, { Autoplay, Pagination } from 'swiper';
import 'swiper/css/bundle';

let swiper_covid = new Swiper('.swiper-covid', {
  modules: [Autoplay, Pagination],
  loop: true,
  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
  },
  speed: 1000,
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 2,
    },
    1280: {
      slidesPerView: 3,
    },
  },
});
