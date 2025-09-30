<?php
	if( ! defined( 'ABSPATH' ) ){	die();	}

	global $avia_config;

	/**
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	get_header();

	do_action( 'ava_after_main_title' );

	/**
	 * @since 5.6.7
	 * @param string $main_class
	 * @param string $context					file name
	 * @return string
	 */
	$main_class = apply_filters( 'avf_custom_main_classes', 'av-main-' . basename( __FILE__, '.php' ), basename( __FILE__ ) );

	?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' );?>'>

			<div class='container'>

				<main class='template-page template-collection content  <?php avia_layout_class( 'content' ); ?> units <?php echo $main_class; ?>' <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'collection' ) );?>>

                    <h1>Test</h1>

				<!--end content-->
				</main>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->

<?php get_footer();

