<?php
	if ( ! defined( 'ABSPATH' ) ){ die(); }

	/**
	* Template Name: Idea Basket
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

	$submit_link = get_field("submit_form_url", get_the_ID());
	?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='template-page content  <?php avia_layout_class( 'content' ); ?> units <?php echo $main_class; ?>' <?php avia_markup_helper(array('context' => 'content','post_type'=>'page'));?>>
				<!--idea basket content-->
				<div class="idea-basket-container">
					<h1>Idea Basket</h1>
					<p id="idea-basket-tagline">You've made some excellent selections! Enquire now and request your samples today.</p>
					<div id="basket-items" class="basket-items-list">
						<!-- Items will be loaded here -->
					</div>
					<div id="empty-basket" class="empty-basket" style="display: none;">
						<p>Your idea basket is empty. Start adding tiles to your basket!</p>
						<a href="<?php echo home_url(); ?>" class="btn btn-primary">Browse Tiles</a>
					</div>
					<div id="submit-btn-container">
						<a href="<?php echo $submit_link; ?>" class="submit-btn">Tile Enquiry</a>
					</div>
				</div>

				<!--end content-->
				</main>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->

<?php
		get_footer();
