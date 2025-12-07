<?php
	if ( ! defined( 'ABSPATH' ) ){ die(); }

	/**
	* Template Name: Submit Tile
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

	?>

		<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/submit-tile.js"></script>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='template-page content  <?php avia_layout_class( 'content' ); ?> units <?php echo $main_class; ?>' <?php avia_markup_helper(array('context' => 'content','post_type'=>'page'));?>>
				<!--idea basket content-->
				<div id="tile-enquiry-container">
					
				</div>

				<form id="tile-enquiry-form">
					<div class="col-flex-row two-col-flex-row">
						<input type="text" name="customer-name" id="customer-name" placeholder="Customer Name" require>
						<select name="customer-type" id="customer-type" placeholder="Customer Type" require>
							<option value="" disabled selected hidden>Please Select Your Role</option>
							<option value="owner">Home Owner</option>
							<option value="designer">Designer</option>
							<option value="architect">Architect</option>
							<option value="builder">Tiler/Builder</option>
							<option value="retailer">Tile Retailer</option>
							<option value="student">Student</option>
							<option value="media">Media</option>
						</select>
						<input type="number" name="contact-no" id="contact-no" placeholder="Contact Number" require>
						<input type="text" name="company-name" id="company-name" placeholder="Company Name">
						<input type="text" name="customer-email" id="customer-email" placeholder="Email Address" require>
						<input type="text" name="project-reference" id="project-reference" placeholder="Project Reference">
					</div>
					<div class="col-flex-row one-col-flex-row">
						<input type="checkbox" id="need-sample" name="need-sample" value="yes"><label>Click here if you need samples for the selected tiles</label>
						<input type="text" name="customer-address" id="customer-address" placeholder="Address" require>
					</div>

					<input type="submit" id="submit-form-btn" value="Send" class="button" data-sending-label="Sending">
				</form>

				<div id="empty-list">
					<p>Please add tiles to your busket first.</p>
				</div>

				<!--end content-->
				</main>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->

<?php
		get_footer();
