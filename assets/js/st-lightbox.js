jQuery(document).ready(function($) {
    // Create lightbox HTML structure
    var lightboxHTML = '<div class="st-lightbox-overlay">' +
        '<div class="st-lightbox-content">' +
        '<button class="st-lightbox-close">&times;</button>' +
        '<img class="st-lightbox-image" src="" alt="">' +
        '</div>' +
        '</div>';

    $('body').append(lightboxHTML);

    var $overlay = $('.st-lightbox-overlay');
    var $image = $('.st-lightbox-image');
    var $closeBtn = $('.st-lightbox-close');

    // Attach click event to all .st-lightbox elements
    $(document).on('click', '.st-lightbox', function(e) {
        e.preventDefault();
        var imageUrl = $(this).attr('href');
        $image.attr('src', imageUrl);
        $overlay.addClass('active');
    });

    // Close lightbox on close button click
    $closeBtn.on('click', function() {
        $overlay.removeClass('active');
    });

    // Close lightbox on overlay click (outside content)
    $overlay.on('click', function(e) {
        if (e.target === this) {
            $overlay.removeClass('active');
        }
    });

    // Close lightbox on ESC key press
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27 && $overlay.hasClass('active')) {
            $overlay.removeClass('active');
        }
    });
});
