<?php
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

/**
 * Template Name: All Tiles
 */

global $avia_config, $wp_query;

/*
 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
 */
get_header();

/**
 * @used_by				enfold\config-wpml\config.php				10
 * @since 4.5.1
 */
do_action( 'ava_page_template_after_header' );

if ( get_post_meta( get_the_ID(), 'header', true ) != 'no' ) {
    echo avia_title();
}

do_action( 'ava_after_main_title' );

/**
 * @since 5.6.7
 * @param string $main_class
 * @param string $context					file name
 * @return string
 */
$main_class    = apply_filters( 'avf_custom_main_classes', 'av-main-' . basename( __FILE__, '.php' ), basename( __FILE__ ) );
$filter_groups = function_exists( 'st_get_all_tiles_filter_config' ) ? st_get_all_tiles_filter_config( get_the_ID() ) : array();

/**
 * Helper to output the ACF-configured filter groups and options.
 */
if ( ! function_exists( 'st_render_all_tiles_filters_html' ) ) {
    function st_render_all_tiles_filters_html( $filter_groups ) {
        if ( empty( $filter_groups ) ) {
            return;
        }
        foreach ( $filter_groups as $group ) {
            echo '<div class="all-tiles-filter-group" data-field="' . esc_attr( $group['field'] ) . '">';
            echo '<span class="category-item tile-filter-heading">' . esc_html( $group['label'] ) . '</span><br>';
            foreach ( $group['options'] as $opt ) {
                echo '<span class="category-item child-category tile-filter-option"'
                    . ' data-option-key="' . esc_attr( $opt['key'] ) . '"'
                    . ' data-field="' . esc_attr( $opt['field'] ) . '"'
                    . ' data-option-name="' . esc_attr( $opt['name'] ) . '">'
                    . '&nbsp;&nbsp;' . esc_html( $opt['name'] )
                    . '</span><br>';
            }
            echo '</div>';
        }
    }
}
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

    <div class='container'>

        <main class='template-page content  <?php avia_layout_class( 'content' ); ?> units <?php echo $main_class; ?>'
            <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'page' ) ); ?>>

            <div class="full-width-breadcrumbs-container container">
                <div class="breadcrumbs tile-breadcrumbs"><?php echo do_shortcode( '[av_breadcrumbs]' ); ?></div>
            </div>

            <!-- Left Column: Filters -->
            <div class="category-list flex_column av_one_fourth avia-builder-el-0 el_before_av_three_fourth avia-builder-el-first first flex_column_div">
                <div class="active-categories" id="all-tiles-active-filters"></div>
                <div class="category-display-list all-tiles-filter-list">
                    <?php if ( ! empty( $filter_groups ) ) : ?>
                        <div class="desktop-fliter desktop-only">
                            <?php st_render_all_tiles_filters_html( $filter_groups ); ?>
                        </div>

                        <div class="accordion accordion-flush mobile-only" id="tile-fliters">
                            <div class="accordion-item">
                                <p class="accordion-header" id="flush-headingTileFilters">
                                    <button class="accordion-button collapsed collection-fliter-btn" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTileFilters" aria-expanded="false" aria-controls="flush-collapseTileFilters">Tile Filters</button>
                                </p>
                                <div id="flush-collapseTileFilters" class="accordion-collapse collapse" aria-labelledby="flush-headingTileFilters" data-bs-parent="#tile-fliters">
                                    <div class="accordion-body">
                                        <?php st_render_all_tiles_filters_html( $filter_groups ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Tile Cards Grid (3 per row, initial 8 rows = 24 cards) -->
            <div class="flex_column av_three_fourth avia-builder-el-2 el_after_av_one_fourth avia-builder-el-last flex_column_div">
                <div class="collection-tiles-container all-tiles-list-container">
                    <div class="all-tiles-status-bar" id="all-tiles-status"></div>
                    <div class="collection-tiles-list all-tiles-list" id="all-tiles-list">
                        <div class="all-tiles-loading-message">Loading tiles...</div>
                    </div>
                    <div class="load-more-container" id="all-tiles-load-more" style="text-align: center; margin: 20px 0; display: none;">
                        <button type="button" class="load-more-tiles-btn btn st-link-button small-style">Load More Tiles</button>
                    </div>
                </div>
            </div>

        <!--end content-->
        </main>

    </div><!--end container-->

</div><!-- close default .container_wrap element -->

<?php
get_footer();
