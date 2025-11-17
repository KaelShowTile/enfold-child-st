var swiperThumb = new Swiper(".collection-inner-slider-thumb", {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});
var swiperPreview = new Swiper(".collection-inner-slider-preview", {
    spaceBetween: 10,
    thumbs: {
    swiper: swiperThumb,
    },
});