<?php
	if ( ! defined( 'ABSPATH' ) ){ die(); }

	/**
	* Template Name: Inspiration
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

	$inspiration_pages_html = null;
	$inspiration_video_banner_html = null;
	$inspiration_explore_design_html = null;
	$inspiration_tile_catalogues_html = null;

	if ( function_exists('get_field') ){
		//Top Slider
		$inspiration_pages_group = get_field('inspiration_pages');

		if($inspiration_pages_group){
			$inspiration_pages_html .= '<div class="container home-feature-page-container">';
			$inspiration_pages_html .= '<div class="home-feature-page-inner">';
			$inspiration_pages_html .= '<div class="swiper" id="feature-page-gallery">';
			$inspiration_pages_html .= '<div class="swiper-wrapper">';
			foreach($inspiration_pages_group as $row){
				$inspiration_pages_html .= '<div class="swiper-slide">';
				$inspiration_pages_html .= '<img src="' . $row['page_thumbnail'] . '">';
				$inspiration_pages_html .= '<h3>' . $row['page_title'] . '</h3>';
				$inspiration_pages_html .= '<p>' . $row['page_description'] . '</p>';
				$inspiration_pages_html .= '<a href="' . $row['page_link'] . '" class="st-link-button small-style">Explore Now</a>';
				$inspiration_pages_html .= '</div>';
			}

			$inspiration_pages_html .= '</div></div></div></div>';
		}

		//Video Block
		$inspiration_video_group = get_field('inspiration_video_banner');
		$banner_video_url = $inspiration_video_group['banner_video_url'];
		$banner_tagline = $inspiration_video_group['banner_tagline'];
		$banner_title = $inspiration_video_group['banner_title'];
		$bannner_description = $inspiration_video_group['bannner_description'];
		$banner_link = $inspiration_video_group['banner_link'];

		if($banner_title && $banner_video_url){
			$inspiration_video_banner_html .= '<div class="container home-banner-container">';
			$inspiration_video_banner_html .= '<div class="flex_column_table sc-av_two_fifth av-equal-height-column-flextable">';

			$inspiration_video_banner_html .= '<div class="flex_column av_two_fifth avia-builder-el-0 el_before_av_three_fifth avia-builder-el-first first flex_column_table_cell av-equal-height-column av-align-top">';

			$inspiration_video_banner_html .= '<div class="banner-top-container"><span>' . $banner_tagline . '</span><h2>' . $banner_title . '</h2>';
			$inspiration_video_banner_html .= '<video width="100%" height="auto" controls muted class="mobile-only"><source src="' . $banner_video_url . '"></video>';
			$inspiration_video_banner_html .= '</div>';

			$inspiration_video_banner_html .= '<div class="banner-bottom-container">';
			$inspiration_video_banner_html .= '<p>' . $bannner_description . '</p><a href="' . $banner_link . '" class="st-link-button small-style">Explore Now &gt;</a>';
			$inspiration_video_banner_html .= '</div></div>';

			$inspiration_video_banner_html .= '<div class="flex_column av_three_fifth avia-builder-el-2 el_after_av_two_fifth avia-builder-el-last flex_column_table_cell av-equal-height-column av-align-top desktop-only">';
			$inspiration_video_banner_html .= '<video width="100%" height="auto" controls muted><source src="' . $banner_video_url . '"></video>';
			$inspiration_video_banner_html .= '</div>';

			$inspiration_video_banner_html .= '</div></div>';
		}

		//Tile Design Slider
		$inspiration_explore_design_group = get_field('inspiration_explore_tile_design');

		if($inspiration_explore_design_group){
			$inspiration_explore_design_html .= '<div class="container inpiration-explore-design-container">';
			$inspiration_explore_design_html .= '<h2>Explore Tile Design</h2>';
			$inspiration_explore_design_html .= '<div class="container inpiration-explore-design-inner">';
			$inspiration_explore_design_html .= '<div class="swiper" id="inspiration-explore-design">';
			$inspiration_explore_design_html .= '<div class="swiper-wrapper">';
			foreach($inspiration_explore_design_group as $row){
				$inspiration_explore_design_html .= '<div class="swiper-slide">';
				$inspiration_explore_design_html .= '<a href="' . $row['tile_design_url'] . '"><img src="' . $row['tile_design_image'] . '"></a>';
				$inspiration_explore_design_html .= '<a href="' . $row['tile_design_url'] . '"><h3>' . $row['tile_design_title'] . '</h3></a>';
				$inspiration_explore_design_html .= '<p>' . $row['tile_design_des'] . '</p>';
				$inspiration_explore_design_html .= '</div>';
			}

			$inspiration_explore_design_html .= '</div></div></div></div>';
		}

		//Tile Catalogues
		$inspiration_tile_catalogues_group = get_field('inspiration_tile_catalogues');

		if($inspiration_tile_catalogues_group){
			$inspiration_tile_catalogues_html .= '<div class="container inpiration-catalogues-container">';
			$inspiration_tile_catalogues_html .= '<h2>Explore Tile Catalogues</h2>';
			$inspiration_tile_catalogues_html .= '<div class="inpiration-catalogues-inner">';
			$catalogue_count = 0;
			foreach($inspiration_tile_catalogues_group as $row){
				$item_style = ($catalogue_count >= 12) ? 'style="display:none;"' : '';
				$item_class = ($catalogue_count >= 12) ? 'inpiration-catalogues-item hidden-catalogue' : 'inpiration-catalogues-item';

				$inspiration_tile_catalogues_html .= '<div class="' . $item_class . '" ' . $item_style . '>';
				$inspiration_tile_catalogues_html .= '<a href="' . $row['catalogue_link'] . '"><img src="' .  $row['catalogue_image'] . '"></a>';
				$inspiration_tile_catalogues_html .= '<a href="' . $row['catalogue_link'] . '"><h3>' . $row['catalogue_name'] . '</h3></a>';
				$inspiration_tile_catalogues_html .= '</div>';
				$catalogue_count++;
			}
			$inspiration_tile_catalogues_html .= '</div>';
			if($catalogue_count > 12){
				$inspiration_tile_catalogues_html .= '<div class="load-more-container" style="text-align: center; margin-top: 20px; width: 100%;"><button id="load-more-catalogues-btn" class="st-link-button small-style">Load More</button></div>';
			}
			$inspiration_tile_catalogues_html .= '</div>';
		}
	}

	?>

		<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/inspiration.js"></script>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>
				
				<h1 class="inspiration-page-title">Inspiration</h1>

				<?php 
				
				if($inspiration_pages_html){
					echo $inspiration_pages_html;
				}

				if($inspiration_video_banner_html){
					echo $inspiration_video_banner_html;
				}

				if($inspiration_explore_design_group){
					echo $inspiration_explore_design_html;
				}

				if($inspiration_tile_catalogues_html){
					echo $inspiration_tile_catalogues_html;
				}

				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->

<?php
		get_footer();
