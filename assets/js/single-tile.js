document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('tile-gallery')) {
        var swiper = new Swiper("#tile-gallery", {
            slidesPerView: "auto",
            spaceBetween: 0,
            grabCursor: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            scrollbar: {
                el: ".swiper-scrollbar",
                clickable: true,
            },
            mousewheel: true,
            keyboard: {
                enabled: true,
            },
        });
    }
});
