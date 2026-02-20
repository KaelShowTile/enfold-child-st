$('.collection-inner-slider-thumb').each(function() {
    var thumbEl = this;
    var previewEl = $(this).siblings('.collection-inner-slider-preview')[0];
    if (previewEl) {
        var swiperThumb = new Swiper(thumbEl, {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiperPreview = new Swiper(previewEl, {
            spaceBetween: 10,
            thumbs: {
                swiper: swiperThumb,
            },
        });
    }
});