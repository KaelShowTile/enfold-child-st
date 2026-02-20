<?php
	if ( ! defined( 'ABSPATH' ) ){ die(); }

	/**
	* Template Name: All Project
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

	if( get_post_meta(get_the_ID(), 'header', true) != 'no')
	{
		echo avia_title();
	}

	do_action( 'ava_after_main_title' );

	/**
	 * @since 5.6.7
	 * @param string $main_class
	 * @param string $context					file name
	 * @return string
	 */
	$main_class = apply_filters( 'avf_custom_main_classes', 'av-main-' . basename( __FILE__, '.php' ), basename( __FILE__ ) );

	$commerical_term = get_term_by('slug', 'commercial', 'project-category');
	$commerical_id = (array)$commerical_term->term_id;
	$residential_term = get_term_by('slug', 'residential', 'project-category');
	$residential_id = (array)$residential_term->term_id;

	?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<div class="tab-container">
					<div role="tablist" class="tab-list">
						<button role="tab" aria-selected="true" aria-controls="panel-1" id="tab-1" class="tab-button">
							Commercial
						</button>
						<button role="tab" aria-selected="false" aria-controls="panel-2" id="tab-2" class="tab-button">
							Residential
						</button>
					</div>

					<div id="panel-1" role="tabpanel" aria-labelledby="tab-1" class="tab-panel">
						<?php echo get_project_html(0, 8, $commerical_id, true); ?>
					</div>
					<div id="panel-2" role="tabpanel" aria-labelledby="tab-2" class="tab-panel" hidden>
						<?php echo get_project_html(0, 8, $residential_id, true); ?>
					</div>
				</div>

				<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/st-tab.js" id="st-tab-js"></script>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->

<?php
		get_footer();
