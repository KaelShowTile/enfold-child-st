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

	$feature_project_output_html = null;
	$other_project_output_html = null;
	$feature_page_output_html = null;
	$blog_slider_output_html = null;

	//max description length
	$project_des_length = 120; 
	$blog_des_length = 180; 

	//ACF value
	if ( function_exists('get_field') ){
		//video slider
		$video_slider_id = get_field('homepage_slider_video_id');

		//banner
		$banner_detail = get_field('homepage_banner');
		$banner_tagline = $banner_detail['homepage_banner_tagline'];
		$banner_title = $banner_detail['homepage_banner_title'];
		$bannner_description = $banner_detail['homepage_bannner_description'];
		$banner_link = $banner_detail['homepage_banner_link'];
		$banner_image_link = get_field('homepage_banner_image');

		//trending video banner
		$trending_video = get_field('homepage_trending_video');
		$trending_video_title = $trending_video['trending_video_section_title'];
		$trending_video_description = $trending_video['trending_video_section_description'];
		$trending_video_link = $trending_video['trending_video_section_link'];
		$trending_video_id = $trending_video['trending_video_id'];

		//project section
		$project_settings = get_field('homepage_project');
		$project_section = false;
		$feature_project_category_id = $project_settings['feature_project_category'];
		$other_project_numbers = $project_settings['number_projects_shows'];

		//feature projects
		if($feature_project_category_id){
			$feature_project_args = array(
				'post_type' => 'project',
				'posts_per_page' => 2,
				'tax_query' => array(
					array(
						'taxonomy' => 'project_category',
						'field'    => 'term_id',
						'terms'    => $feature_project_category_id,
					),
				),
			);
			$feature_project_query = new WP_Query($feature_project_args);

			if ($feature_project_query->have_posts()){
				$feature_project_output_html .= '<h2>Latest Tile Projects and Collaborations</h2>';
				$feature_project_output_html .= '<div class="home-feature-project-inner">';
				$project_section = true;
				
				while ($feature_project_query->have_posts()){
					$feature_project_query->the_post();
					$project_id = get_the_ID();
					$project_title = get_the_title();
					$project_description = get_field('project_description', $project_id);
					//limit the length of description
					if (strlen($project_description) > $project_des_length) {
						$project_description = substr($project_description, 0, strrpos(substr($project_description, 0, $project_des_length), ' ')) . '...';
					}
					$project_link = get_permalink();
            		$project_thumb = get_the_post_thumbnail_url($project_id, 'large');

					$feature_project_output_html .= '<div class="flex_column av_one_half flex_column_div">';
					$feature_project_output_html .= '<img src="' . $project_thumb . '">';
					$feature_project_output_html .= '<h3>' . $project_title . '</h3>';
					$feature_project_output_html .= '<p>' . $project_description . '</p>';
					$feature_project_output_html .= '<a href="' . $project_link . '" class="st-link-button small-style">Explore Now</a>';
					$feature_project_output_html .= '</div>';
				}

				$feature_project_output_html .= '</div>';
			}

			wp_reset_postdata();
		}

		//other project
		if($other_project_numbers > 0){
		
			$other_project_args = array(
				'post_type' => 'project',
				'posts_per_page' => $other_project_numbers,
				'tax_query' => array(
					array(
						'taxonomy' => 'project_category',
						'field'    => 'term_id',
						'terms'    => $feature_project_category_id,
						'operator' => 'NOT IN',
					),
				),
			);
			$other_project_query = new WP_Query($other_project_args);

			if ($feature_project_query->have_posts()){
				if($project_section == false){
					$other_project_output_html .= '<h2>Latest Tile Projects and Collaborations</h2>';
				}

				$other_project_output_html .= '<div class="home-other-project-inner">';
				$other_project_output_html .= '<div class="swiper" id="other-project-slider">';
				$other_project_output_html .= '<div class="swiper-wrapper">';

				while ($other_project_query ->have_posts()){
					$other_project_query ->the_post();
					$project_id = get_the_ID();
					$project_type = get_field('project_type', $project_id);
					$project_title = get_the_title();
					$project_description = get_field('project_description', $project_id);
					//limit the length of description
					if (strlen($project_description) > $project_des_length) {
						$project_description = substr($project_description, 0, strrpos(substr($project_description, 0, $project_des_length), ' ')) . '...';
					}
					$project_link = get_permalink();
            		$project_thumb = get_the_post_thumbnail_url($project_id, 'large');

					$other_project_output_html .= '<div class="swiper-slide">';
					$other_project_output_html .= '<a href="' . $project_link . '" ><img src="' . $project_thumb . '"></a>';
					$other_project_output_html .= '<span>' . $project_type . '</span>';
					$other_project_output_html .= '<a href="' . $project_link . '" ><h3>' . $project_title . '</h3></a>';
					$other_project_output_html .= '<p>' . $project_description . ' <a href="' . $project_link . '" >View Full Project</a></p>';
					$other_project_output_html .= '</div>';
				}

				$other_project_output_html .= '</div></div></div>';
			}

			wp_reset_postdata();
		}

		//feature page slider section
		$feature_page_rows = get_field('feature_page_links');
		if($feature_page_rows){
			$feature_page_output_html .= '<h2>Get Inspired</h2>';
			$feature_page_output_html .= '<div class="home-feature-page-inner">';
			$feature_page_output_html .= '<div class="swiper" id="feature-page-gallery">';
			$feature_page_output_html .= '<div class="swiper-wrapper">';
			foreach($feature_page_rows as $row){
				$feature_page_output_html .= '<div class="swiper-slide">';
				$feature_page_output_html .= '<img src="' . $row['page_thumbnail'] . '">';
				$feature_page_output_html .= '<h3>' . $row['page_title'] . '</h3>';
				$feature_page_output_html .= '<p>' . $row['page_description'] . '</p>';
				$feature_page_output_html .= '<a href="' . $row['page_link'] . '" class="st-link-button small-style">Explore Now</a>';
				$feature_page_output_html .= '</div>';
			}
			$feature_page_output_html .= '</div>';
			$feature_page_output_html .= '<div class="swiper-pagination"></div>';
			$feature_page_output_html .= '</div></div>';
		}

		//blog slider section
		$blog_slider = get_field('home_blog_section');
		$blog_category = $blog_slider['homepage_blog_category'];
		$blog_number = $blog_slider['homepage_blog_post_number'];

		if($blog_number > 0){
			$blog_args = array(
				'post_type' => 'post',
				'posts_per_page' => $blog_number,
				'category__in' => $blog_category,
			);
			$blog_query = new WP_Query($blog_args);

			if ($blog_query->have_posts()){

				$blog_slider_output_html .= '<div class="home-blog-inner">';
				$blog_slider_output_html .= '<div class="swiper" id="home-blog-slider">';
				$blog_slider_output_html .= '<div class="swiper-wrapper">';

				while ($blog_query->have_posts()){
					$blog_query->the_post();
					$blog_title = get_the_title();
					$blog_description =get_the_excerpt();
					if (strlen($blog_description) > $blog_des_length) {
						$blog_description = substr($blog_description, 0, strrpos(substr($blog_description, 0, $blog_des_length), ' ')) . '...';
					}
					$blog_link = get_permalink();
					$blog_id = get_the_ID();
            		$blog_thumb = get_the_post_thumbnail_url($blog_id, 'large');

					$blog_slider_output_html .= '<div class="swiper-slide">';
					$blog_slider_output_html .= '<img src="' . $blog_thumb . '">';
					$blog_slider_output_html .= '<h3>' . $blog_title . '</h3>';
					$blog_slider_output_html .= '<p>' . $blog_description . '</p>';
					$blog_slider_output_html .= '<a href="' . $blog_link . '" class="st-link-button small-style">Explore Now</a>';
					$blog_slider_output_html .= '</div>';
				}

				$blog_slider_output_html .= '</div></div></div>';
			}

			wp_reset_postdata();
		}
	}
	
	?>

	<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/homepage.js"></script>

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
						<a href="<?php echo $banner_link; ?>" class="st-link-button small-style">Explore Now ></a>
					</div>
				</div>

				<div class="flex_column av_three_fifth avia-builder-el-2 el_after_av_two_fifth avia-builder-el-last flex_column_table_cell av-equal-height-column av-align-top">
					<img src="<?php echo $banner_image_link; ?>">	
				</div>
			</div>

		</div>
		
		<?php if($feature_project_output_html): ?>
		<div class="container home-feature-project-container">
			<?php echo $feature_project_output_html; ?>
		</div>
		<?php endif; ?>

		<?php if($other_project_output_html): ?>
		<div class="container home-other-project-container">
			<?php echo $other_project_output_html; ?>
		</div>
		<?php endif; ?>

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

		<?php if($feature_page_rows): ?>
		<div class="container home-feature-page-container">
			<?php echo $feature_page_output_html; ?>
		</div>
		<?php endif; ?>

		<?php if($blog_slider_output_html): ?>
		<div class="container home-blog-lislt-container">
			<?php echo $blog_slider_output_html; ?>
		</div>
		<?php endif; ?>

	</div><!-- close default .container_wrap element -->

<?php
		get_footer();
