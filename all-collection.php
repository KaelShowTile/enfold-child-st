<?php
	if ( ! defined( 'ABSPATH' ) ){ die(); }

	/**
	* Template Name: All collection
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

	$collection_category_ids = [];

	?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='template-page content  <?php avia_layout_class( 'content' ); ?> units <?php echo $main_class; ?>' <?php avia_markup_helper(array('context' => 'content','post_type'=>'page'));?>>

					<div class="category-list flex_column av_one_fourth  avia-builder-el-0  el_before_av_three_fourth  avia-builder-el-first  first flex_column_div">
						<div class="active-categories"></div>
						<div class="category-display-list">
							<?php
							$terms = get_terms( array(
								'taxonomy' => 'collection_category',
								'hide_empty' => false,
							) );

							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								display_terms_hierarchically( $terms );
							}
							?>
						</div>
					</div>

					<div class="flex_column av_three_fourth avia-builder-el-2 el_after_av_one_fourth avia-builder-el-last flex_column_div">
						<?php echo get_collections_html(0, 6, null, true); ?>
					</div>

				<!--end content-->
				</main>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->

<?php
		get_footer();
