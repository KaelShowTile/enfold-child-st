<?php
//Child theme setting
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
function enqueue_child_theme_styles() 
{
  	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// Images auto-crop quality
add_filter('wp_editor_set_quality', function($quality, $mime_type) {
    if ($mime_type === 'image/jpeg') return 60;     // JPEG
    if ($mime_type === 'image/webp') return 60;     // WebP
    if ($mime_type === 'image/png') return 6;       // PNG compression level
    return $quality; // Default for others
}, 10, 2);

