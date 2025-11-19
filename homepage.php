<?php
	if ( ! defined( 'ABSPATH' ) ){ die(); }

	/**
	* Template Name: Homepage
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

	//default ACF values
	$video_slider_id = "1095801320";

	$banner_tagline = "";
	$banner_title = "";
	$bannner_description = "";
	$banner_link = "";
	$banner_image_link ="";

	$trending_video_title ="";
	$trending_video_description ="";
	$trending_video_link ="";
	$trending_video_id ="";

	//check if ACF actived, then get value
	if ( function_exists('get_field') ){
		$video_slider_id = get_field('homepage_slider_video_id');

		$banner_detail = get_field('homepage_banner');
		$banner_tagline = $banner_detail['homepage_banner_tagline'];
		$banner_title = $banner_detail['homepage_banner_title'];
		$bannner_description = $banner_detail['homepage_bannner_description'];
		$banner_link = $banner_detail['homepage_banner_link'];
		$banner_image_link = get_field('homepage_banner_image');

		$trending_video = get_field('homepage_trending_video');
		$trending_video_title = $trending_video['trending_video_section_title'];
		$trending_video_description = $trending_video['trending_video_section_description'];
		$trending_video_link = $trending_video['trending_video_section_link'];
		$trending_video_id = $trending_video['trending_video_id'];
	}
	
	?>

	<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/vimeo.js"></script>

	<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

		<div class="st-video-container">
			<div class="st-video-wrapper">
				<div class="st-video-iframe-container">
					
					<iframe src="https://player.vimeo.com/video/<?php echo $video_slider_id; ?>?background=1&autoplay=1&loop=1&muted=1&autopause=0" frameborder="0" allow="autoplay; fullscreen" allowfullscreen>
					</iframe>
				</div>
			</div>
			
		</div>

		<div class='container home-banner-container'>
			
			<div class="flex_column_table sc-av_two_fifth av-equal-height-column-flextable">
				<div class="flex_column av_two_fifth avia-builder-el-0 el_before_av_three_fifth avia-builder-el-first first flex_column_table_cell av-equal-height-column av-align-top">
					<div class="banner-top-container">
						<span><?php echo $banner_tagline; ?></span>
						<h2><?php echo $banner_title; ?></h2>
					</div>
					<div class="banner-bottom-container">
						<p><?php echo $bannner_description; ?></p>
						<a href="<?php echo $banner_link; ?>" class="st-link-button">Explore Now ></a>
					</div>
				</div>

				<div class="flex_column av_three_fifth avia-builder-el-2 el_after_av_two_fifth avia-builder-el-last flex_column_table_cell av-equal-height-column av-align-top">
					<img src="<?php echo $banner_image_link; ?>">	
				</div>
			</div>

		</div>

		<div class="st-video-container">
			<div class="st-video-wrapper">
				<div class="st-video-iframe-container">
					
					<iframe src="https://player.vimeo.com/video/<?php echo $trending_video_id; ?>?background=1&autoplay=1&loop=1&muted=1&autopause=0" frameborder="0" allow="autoplay; fullscreen" allowfullscreen>
					</iframe>
				</div>
			</div>
			
			<!-- Optional content overlay -->
			<div class="st-overlay bottom-left">
				<div class="st-overlay-inner">
					<span class="tagline">TRENDING</span>
					<h2><?php echo $trending_video_title; ?></h2>
					<p><?php echo $trending_video_description; ?></p>
					<a href="<?php echo $trending_video_link; ?>" class="st-link-button white-color-schema">Explore Now ></a>
				</div>
			</div>
		</div>

	</div><!-- close default .container_wrap element -->

<?php
		get_footer();
