document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('other-project-slider')) {
        var swiper = new Swiper("#other-project-slider", {
            slidesPerView: "auto",
            spaceBetween: 10,
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