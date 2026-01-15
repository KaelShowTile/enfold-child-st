jQuery(document).ready(function($) {
    // Create lightbox HTML structure
    var lightboxHTML = '<div class="st-lightbox-overlay">' +
        '<div class="st-lightbox-content">' +
        '<button class="st-lightbox-close">&times;</button>' +
        '<img class="st-lightbox-image" src="" alt="" style="display: none;">' +
        '<video class="st-lightbox-video" controls style="display: none;"></video>' +
        '</div>' +
        '</div>';

    $('body').append(lightboxHTML);

    var $overlay = $('.st-lightbox-overlay');
    var $image = $('.st-lightbox-image');
    var $video = $('.st-lightbox-video');
    var $closeBtn = $('.st-lightbox-close');

    // Use event capturing to prevent redirect before Enfold's handlers
    document.addEventListener('click', function(e) {
        var lightboxLink = e.target.closest('.st-lightbox');
        if (lightboxLink) {
            e.preventDefault();
            e.stopPropagation();
            var mediaUrl = lightboxLink.getAttribute('href');
            var isVideo = mediaUrl.toLowerCase().endsWith('.mp4') || mediaUrl.toLowerCase().endsWith('.flv');

            if (isVideo) {
                $image.hide();
                $video.show();
                $video.attr('src', mediaUrl);
                $video[0].load(); // Reload video

                // Handle video sizing based on aspect ratio
                $video.off('loadedmetadata').on('loadedmetadata', function() {
                    var vid = $video[0];
                    var aspect = vid.videoWidth / vid.videoHeight;
                    if (aspect > 1.4) {
                        // Horizontal video: use full width
                        vid.style.width = '100%';
                        vid.style.height = 'auto';
                    } else {
                        // Vertical/square video: use full height
                        vid.style.width = 'auto';
                        vid.style.height = '90vh';
                    }
                });
            } else {
                $video.hide();
                $image.show();
                $image.attr('src', mediaUrl);
            }
            $overlay.addClass('active');
            return false; // Additional prevention
        }
    }, {capture: true});

    // Close lightbox on close button click
    $closeBtn.on('click', function() {
        $video[0].pause();
        $overlay.removeClass('active');
    });

    // Close lightbox on overlay click (outside content)
    $overlay.on('click', function(e) {
        if (e.target === this) {
            $video[0].pause();
            $overlay.removeClass('active');
        }
    });

    // Close lightbox on ESC key press
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27 && $overlay.hasClass('active')) {
            $video[0].pause();
            $overlay.removeClass('active');
        }
    });
});
