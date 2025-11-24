function adjustVideoCover() {
    const iframe = document.querySelector('.st-video-iframe-container iframe');
    const container = document.querySelector('.st-video-container');
    
    const containerWidth = container.offsetWidth;
    const containerHeight = container.offsetHeight;
    const containerAspectRatio = containerWidth / containerHeight;
    
    // Vimeo default aspect ratio is 16:9 = 1.777...
    const videoAspectRatio = 16 / 9;
    
    let scale;
    
    if (containerAspectRatio > videoAspectRatio) {
        // Container is wider than video - scale based on height
        scale = (containerWidth / videoAspectRatio) / containerHeight;
    } else {
        // Container is taller than video - scale based on width
        scale = (containerHeight * videoAspectRatio) / containerWidth;
    }
    
    // Apply the scale transform
    iframe.style.transform = `scale(${scale})`;
    iframe.style.transformOrigin = 'center';
}

// Adjust on load and resize
window.addEventListener('load', adjustVideoCover);
window.addEventListener('resize', adjustVideoCover);

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

    const homeProjectSlider = document.getElementById('other-project-slider');
    if (homeProjectSlider) {
        const swiper = new Swiper('#other-project-slider', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 50,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    }

    //blog post slider
    const blogPostSlider = document.getElementById('home-blog-slider');
    if (blogPostSlider) {
        const swiper = new Swiper('#home-blog-slider', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 50,
        });
    }
});
