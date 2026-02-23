// Initialize Swiper
document.addEventListener('DOMContentLoaded', function () {
    //feature page slider
    const featurePageSlider = document.getElementById('feature-page-gallery');
    if (featurePageSlider) {
        const swiper = new Swiper('#feature-page-gallery', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 50,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    }

    const inspirationExploreSslider = document.getElementById('inspiration-explore-design');
    if (inspirationExploreSslider) {
        const swiper = new Swiper('#inspiration-explore-design', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 30,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    }

});

//load more btn
jQuery(document).ready(function($) {
    $('#load-more-catalogues-btn').on('click', function(e) {
        e.preventDefault();
        
        // Select the hidden items
        var hiddenItems = $('.hidden-catalogue');
        
        // Show the next 6 items
        hiddenItems.slice(0, 12).fadeIn().removeClass('hidden-catalogue');
        
        // If no more hidden items, hide the button
        if ($('.hidden-catalogue').length === 0) {
            $(this).parent().fadeOut();
        }
    });
});
