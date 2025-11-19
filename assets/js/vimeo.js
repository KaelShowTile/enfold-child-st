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