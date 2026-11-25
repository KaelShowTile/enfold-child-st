<?php
/**
 * All Tiles Static JSON Feed Generator, WP-Cron, and Enqueue Handler
 *
 * Generates wp-content/uploads/tiles.json daily via WP-Cron so all-tile.php
 * can perform instant client-side filtering without hitting the database on every filter click.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get the file path and URL for tiles.json
 *
 * @return array{path: string, url: string}
 */
function st_get_tiles_feed_info() {
    $upload_dir = wp_upload_dir();
    return array(
        'path' => trailingslashit( $upload_dir['basedir'] ) . 'tiles.json',
        'url'  => trailingslashit( $upload_dir['baseurl'] ) . 'tiles.json',
    );
}

/**
 * Generate the static tiles.json feed for all published 'tile' posts (excludes 'private-tile').
 * Ordered by publish date DESC, ID DESC.
 *
 * @return array{success: bool, count: int, path: string, bytes: int, error?: string}
 */
function st_generate_tiles_json() {
    @set_time_limit( 300 );

    $feed_info = st_get_tiles_feed_info();
    $json_file = $feed_info['path'];
    $tmp_file  = $json_file . '.tmp';

    $query = new WP_Query( array(
        'post_type'              => 'tile',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'orderby'                => array(
            'date' => 'DESC',
            'ID'   => 'DESC',
        ),
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ) );

    $tile_ids = $query->posts;
    $tiles    = array();

    // Cache attachment URL lookups across tiles to avoid duplicate DB hits
    $img_url_cache = array();
    $get_img_url   = function ( $attachment_id, $size = 'medium' ) use ( &$img_url_cache ) {
        $attachment_id = intval( $attachment_id );
        if ( $attachment_id <= 0 ) {
            return '';
        }
        $cache_key = $attachment_id . ':' . $size;
        if ( ! array_key_exists( $cache_key, $img_url_cache ) ) {
            $url = wp_get_attachment_image_url( $attachment_id, $size );
            $img_url_cache[ $cache_key ] = $url ? $url : '';
        }
        return $img_url_cache[ $cache_key ];
    };

    if ( ! empty( $tile_ids ) ) {
        // Pre-warm attachment meta cache for featured images and finish images
        $all_attachment_ids = array();
        $meta_by_tile       = array();

        foreach ( $tile_ids as $tile_id ) {
            $meta = get_post_meta( $tile_id );
            $meta_by_tile[ $tile_id ] = $meta;

            if ( ! empty( $meta['_thumbnail_id'][0] ) ) {
                $all_attachment_ids[] = intval( $meta['_thumbnail_id'][0] );
            }
            $finish_count = isset( $meta['tile_finish'][0] ) ? intval( $meta['tile_finish'][0] ) : 0;
            for ( $i = 0; $i < $finish_count; $i++ ) {
                $fin_img_key = 'tile_finish_' . $i . '_finish_image';
                if ( ! empty( $meta[ $fin_img_key ][0] ) ) {
                    $all_attachment_ids[] = intval( $meta[ $fin_img_key ][0] );
                }
            }
        }

        if ( ! empty( $all_attachment_ids ) ) {
            $all_attachment_ids = array_values( array_unique( array_filter( $all_attachment_ids ) ) );
            _prime_post_caches( $all_attachment_ids, false, true );
        }

        foreach ( $tile_ids as $tile_id ) {
            $meta = $meta_by_tile[ $tile_id ];

            // Main thumbnail (medium, fallback to full; empty string lets JS use placeholder)
            $thumb_id  = isset( $meta['_thumbnail_id'][0] ) ? intval( $meta['_thumbnail_id'][0] ) : 0;
            $tile_img  = '';
            if ( $thumb_id > 0 ) {
                $medium_url = $get_img_url( $thumb_id, 'medium' );
                $full_url   = $get_img_url( $thumb_id, 'full' );
                $tile_img   = ( $medium_url !== '' && $medium_url !== $full_url ) ? $medium_url : $full_url;
            }

            // Indent item checkbox (ACF stores serialized array e.g. ['yes'])
            $indent_raw = isset( $meta['indent_item'][0] ) ? maybe_unserialize( $meta['indent_item'][0] ) : '';
            $is_indent  = false;
            if ( is_array( $indent_raw ) ) {
                $is_indent = ! empty( $indent_raw );
            } elseif ( ! empty( $indent_raw ) ) {
                $is_indent = true;
            }

            // Top-level ACF filterable text fields
            $design    = isset( $meta['tile_design'][0] ) ? trim( (string) $meta['tile_design'][0] ) : '';
            $thickness = isset( $meta['tile_thickness'][0] ) ? trim( (string) $meta['tile_thickness'][0] ) : '';
            $material  = isset( $meta['tile_material'][0] ) ? trim( (string) $meta['tile_material'][0] ) : '';

            // Repeater: tile_finish
            $finish_count = isset( $meta['tile_finish'][0] ) ? intval( $meta['tile_finish'][0] ) : 0;
            $finishes     = array();
            $has_360      = false;

            for ( $i = 0; $i < $finish_count; $i++ ) {
                $fn_key   = 'tile_finish_' . $i . '_finish_name';
                $code_key = 'tile_finish_' . $i . '_product_code';
                $sr_key   = 'tile_finish_' . $i . '_tile_slip_rate';
                $img_key  = 'tile_finish_' . $i . '_finish_image';
                $vt_key   = 'tile_finish_' . $i . '_visual_theatre';
                $sz_key   = 'tile_finish_' . $i . '_tile_size';

                $fn_val   = isset( $meta[ $fn_key ][0] ) ? trim( (string) $meta[ $fn_key ][0] ) : '';
                $code_val = isset( $meta[ $code_key ][0] ) ? trim( (string) $meta[ $code_key ][0] ) : '';
                $sr_val   = isset( $meta[ $sr_key ][0] ) ? trim( (string) $meta[ $sr_key ][0] ) : '';
                $vt_val   = isset( $meta[ $vt_key ][0] ) ? trim( (string) $meta[ $vt_key ][0] ) : '';

                if ( $vt_val !== '' ) {
                    $has_360 = true;
                }

                $fin_img_id  = isset( $meta[ $img_key ][0] ) ? intval( $meta[ $img_key ][0] ) : 0;
                $fin_img_url = $fin_img_id > 0 ? $get_img_url( $fin_img_id, 'medium' ) : '';

                $size_count = isset( $meta[ $sz_key ][0] ) ? intval( $meta[ $sz_key ][0] ) : 0;
                $sizes      = array();
                for ( $j = 0; $j < $size_count; $j++ ) {
                    $size_name_key = $sz_key . '_' . $j . '_tile_size_name';
                    $size_name_val = isset( $meta[ $size_name_key ][0] ) ? trim( (string) $meta[ $size_name_key ][0] ) : '';
                    if ( $size_name_val !== '' ) {
                        $sizes[] = $size_name_val;
                    }
                }

                $finish_entry = array(
                    'fn' => $fn_val,
                    'c'  => $code_val,
                    'sr' => $sr_val,
                    'sz' => $sizes,
                );
                // Only store finish image if present and different from main tile image
                if ( $fin_img_url !== '' && $fin_img_url !== $tile_img ) {
                    $finish_entry['img'] = $fin_img_url;
                }

                $finishes[] = $finish_entry;
            }

            $tile_entry = array(
                'id' => intval( $tile_id ),
                't'  => html_entity_decode( get_the_title( $tile_id ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
                'u'  => get_permalink( $tile_id ),
            );

            if ( $tile_img !== '' ) {
                $tile_entry['img'] = $tile_img;
            }
            if ( $is_indent ) {
                $tile_entry['ind'] = 1;
            }
            if ( $has_360 ) {
                $tile_entry['v360'] = 1;
            }
            if ( $design !== '' ) {
                $tile_entry['d'] = $design;
            }
            if ( $thickness !== '' ) {
                $tile_entry['th'] = $thickness;
            }
            if ( $material !== '' ) {
                $tile_entry['m'] = $material;
            }
            if ( ! empty( $finishes ) ) {
                $tile_entry['f'] = $finishes;
            }

            $tiles[] = $tile_entry;
        }
    }

    $json_payload = wp_json_encode( $tiles, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    if ( $json_payload === false ) {
        return array(
            'success' => false,
            'count'   => 0,
            'path'    => $json_file,
            'bytes'   => 0,
            'error'   => 'Failed to encode tiles JSON.',
        );
    }

    $written = @file_put_contents( $tmp_file, $json_payload, LOCK_EX );
    if ( $written === false ) {
        return array(
            'success' => false,
            'count'   => 0,
            'path'    => $json_file,
            'bytes'   => 0,
            'error'   => 'Failed to write temporary tiles JSON file.',
        );
    }

    if ( ! @rename( $tmp_file, $json_file ) ) {
        @unlink( $tmp_file );
        return array(
            'success' => false,
            'count'   => 0,
            'path'    => $json_file,
            'bytes'   => 0,
            'error'   => 'Failed to move temporary tiles JSON file into place.',
        );
    }

    return array(
        'success' => true,
        'count'   => count( $tiles ),
        'path'    => $json_file,
        'bytes'   => strlen( $json_payload ),
    );
}

// Schedule daily tiles JSON generation at 3:30 AM (staggered after collections XML at 3:00 AM)
if ( ! wp_next_scheduled( 'st_generate_tiles_json_cron' ) ) {
    wp_schedule_event( strtotime( '03:30:00' ), 'daily', 'st_generate_tiles_json_cron' );
}
add_action( 'st_generate_tiles_json_cron', 'st_generate_tiles_json' );

/**
 * Helper: map ACF fliter_name field value to display heading label.
 *
 * @param string $field_key ACF select value.
 * @return string Display label.
 */
function st_get_tile_filter_group_label( $field_key ) {
    $labels = array(
        'tile_design'    => 'Design',
        'tile_thickness' => 'Thickness',
        'tile_material'  => 'Material',
        'finish_name'    => 'Finish Name',
        'tile_slip_rate' => 'Slip Rate',
        'tile_size'      => 'Size',
    );
    return isset( $labels[ $field_key ] ) ? $labels[ $field_key ] : ucwords( str_replace( '_', ' ', $field_key ) );
}

/**
 * Read and structure the ACF 'fliters' configuration for an All Tiles page.
 *
 * @param int $page_id Page ID using all-tile.php template.
 * @return array Structured filter groups.
 */
function st_get_all_tiles_filter_config( $page_id ) {
    $raw_filters = function_exists( 'get_field' ) ? get_field( 'fliters', $page_id ) : array();
    if ( empty( $raw_filters ) || ! is_array( $raw_filters ) ) {
        return array();
    }

    $groups = array();
    foreach ( $raw_filters as $g_idx => $group_row ) {
        $field_key = isset( $group_row['fliter_name'] ) ? trim( (string) $group_row['fliter_name'] ) : '';
        if ( $field_key === '' ) {
            continue;
        }

        $raw_options = isset( $group_row['fliter_options'] ) && is_array( $group_row['fliter_options'] )
            ? $group_row['fliter_options']
            : array();

        $options = array();
        foreach ( $raw_options as $o_idx => $opt_row ) {
            $opt_name  = isset( $opt_row['fliter_options_name'] ) ? trim( (string) $opt_row['fliter_options_name'] ) : '';
            $opt_value = isset( $opt_row['fliter_options_value'] ) ? trim( (string) $opt_row['fliter_options_value'] ) : '';

            if ( $opt_name === '' ) {
                continue;
            }
            // Fallback to option display name if option value textarea was left blank
            if ( $opt_value === '' ) {
                $opt_value = $opt_name;
            }

            // Split comma-separated (and newline-separated) values
            $split_vals = preg_split( '/[,\r\n]+/', $opt_value );
            $values     = array();
            if ( is_array( $split_vals ) ) {
                foreach ( $split_vals as $v ) {
                    $v = trim( $v );
                    if ( $v !== '' ) {
                        $values[] = $v;
                    }
                }
            }

            if ( empty( $values ) ) {
                continue;
            }

            $options[] = array(
                'key'    => $field_key . '__' . $g_idx . '_' . $o_idx,
                'field'  => $field_key,
                'name'   => $opt_name,
                'values' => $values,
            );
        }

        if ( ! empty( $options ) ) {
            $groups[] = array(
                'field'   => $field_key,
                'label'   => st_get_tile_filter_group_label( $field_key ),
                'options' => $options,
            );
        }
    }

    return $groups;
}

/**
 * Enqueue All Tiles JS and localize feed/filter data on pages using all-tile.php.
 */
add_action( 'wp_enqueue_scripts', 'st_enqueue_all_tile_scripts' );
function st_enqueue_all_tile_scripts() {
    if ( ! is_page_template( 'all-tile.php' ) ) {
        return;
    }

    $feed_info = st_get_tiles_feed_info();

    // Allow administrators to force-refresh tiles.json via ?refresh_tiles_feed=1
    if ( isset( $_GET['refresh_tiles_feed'] ) && $_GET['refresh_tiles_feed'] === '1' && current_user_can( 'manage_options' ) ) {
        st_generate_tiles_json();
    } elseif ( ! file_exists( $feed_info['path'] ) ) {
        // Fallback: generate once only if tiles.json does not exist yet
        st_generate_tiles_json();
    }

    $mtime = file_exists( $feed_info['path'] ) ? (string) filemtime( $feed_info['path'] ) : '1';
    $js_path = get_stylesheet_directory() . '/assets/js/all-tile.js';
    $js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : '1.0.0';

    wp_enqueue_script(
        'all-tile-js',
        get_stylesheet_directory_uri() . '/assets/js/all-tile.js',
        array( 'jquery' ),
        $js_ver,
        true
    );

    $page_id = get_queried_object_id();

    wp_localize_script( 'all-tile-js', 'all_tile_data', array(
        'feed_url'        => add_query_arg( 'v', $mtime, $feed_info['url'] ),
        'feed_version'    => $mtime,
        'placeholder_img' => get_stylesheet_directory_uri() . '/assets/img/st-place-holder.jpg',
        'basket_icon'     => get_stylesheet_directory_uri() . '/assets/img/add-to-basket-2.svg',
        'icon_360'        => 'https://showtile.com.au/wp-content/uploads/2026/06/360-icon-for-tile-face.svg',
        'page_size'       => 24, // 8 rows x 3 cards per row
        'filter_groups'   => st_get_all_tiles_filter_config( $page_id ),
    ) );
}
