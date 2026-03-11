<?php
	if( ! defined( 'ABSPATH' ) )	{ die(); }

	global $avia_config;

	/**
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	get_header();
    $project_id = get_the_ID();

	$title = __( 'Project', 'avia_framework' ); //default blog title
	$t_link = home_url( '/' );
	$t_sub = '';

	if( avia_get_option( 'frontpage' ) && $new = avia_get_option( 'blogpage' ) )
	{
		$title = get_the_title( $new ); //if the blog is attached to a page use this title
		$t_link = get_permalink( $new );
		$t_sub = avia_post_meta( $new, 'subtitle' );
	}

	do_action( 'ava_after_main_title' );

	/**
	 * @since 5.6.7
	 * @param string $main_class
	 * @param string $context					file name
	 * @return string
	 */
	$main_class = apply_filters( 'avf_custom_main_classes', 'av-main-' . basename( __FILE__, '.php' ), basename( __FILE__ ) );

	$project_images = get_field('project_photos');
	$project_related_tile = get_field('related_tile');
	$project_testimonial = get_field('project_testimonial');

	$project_images_output = null;

	if($project_images){
		$project_images_output .= '<div class="project-gallery-container project-container container">';
		$project_images_output .= '<div class="swiper" id="tile-gallery">';
		$project_images_output .= '<div class="swiper-wrapper">';
		foreach($project_images as $image_id):
			$project_images_output .= '<div class="swiper-slide"><a href="' . wp_get_attachment_image_url($image_id, 'full') . '">' . wp_get_attachment_image( $image_id, 'full') . '</a></div>';
		endforeach;
		$project_images_output .= '</div>';
		$project_images_output .= '<div class="swiper-button-prev"></div>';
		$project_images_output .= '<div class="swiper-button-next"></div>';
		$project_images_output .= '<div class="swiper-scrollbar"></div>';
		$project_images_output .= '</div></div>';
	}
	 
?>

<div class="container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>">

	<div class="container template-project template-single-project">

		<main class="content units <?php avia_layout_class( 'content' ); ?> <?php echo avia_blog_class_string(); ?> <?php echo $main_class; ?>" <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'post' ) );?>>

			<div class="project-title-container container">	
				<h1 class="item-title"><?php the_title(); ?></h1>
				<div class="breadcrumbs project-breadcrumbs"><?php echo do_shortcode('[av_breadcrumbs]'); ?></div>
			</div>

			<!-- Project gallery -->
			<?php if($project_images_output):
				echo $project_images_output;
			endif; ?>

			<!-- Project description -->
			<div class="project-description-container project-container container">
				<div class="flex_column av_one_half avia-builder-el-1 el_before_av_one_half avia-builder-el-first first flex_column_div">
					<?php the_field('project_description'); ?>
				</div>
				<div class="flex_column av_one_half avia-builder-el-2 el_after_av_one_half avia-builder-el-last flex_column_div">
					<div class="collection-description-container">
						<?php if(get_field('project_designer')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Designer</p>
								<p><?php the_field('project_designer') ?></p>
							</div>
						<?php endif; ?>
						<?php if(get_field('project_builder')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Builder</p>
								<p><?php the_field('project_builder') ?></p>
							</div>
						<?php endif; ?>
						<?php if(get_field('project_developer')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Developer</p>
								<p><?php the_field('project_developer') ?></p>
							</div>
						<?php endif; ?>
						<?php if(get_field('project_architect')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Architect</p>
								<p><?php the_field('project_architect') ?></p>
							</div>
						<?php endif; ?>
						<?php if(get_field('project_stylist')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Stylist</p>
								<p><?php the_field('project_stylist') ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
				
			<!-- Project Video -->
			<div class="project-video-container st_container_fullsize">
				<?php if(have_rows('project_video')):?>
					<?php while( have_rows('project_video')): the_row();?>
						<?php if(get_sub_field('project_video_title')): ?>
							<h5 class="project-video-title"><?php the_sub_field('project_video_title'); ?></h5>
						<?php endif; ?>	
						<?php if(get_sub_field('project_video_url')): ?>
							<?php $video_shortcode = "[av_video src='" . get_sub_field('project_video_url') . "' video_loop='aviaTBvideo_loop' video_controls='aviaTBvideo_controls' format='16-9' width='16' height='9' id='' sc_version='1.0']"; ?>
							<?php echo do_shortcode($video_shortcode); ?>
						<?php endif; ?>	
					<?php endwhile; ?>
				<?php endif; ?>		
			</div>

			<!-- House Tour -->
			<?php if( get_field('project_house_tour') ): ?>
				<div class="project-house-tour-container container">
					<?php if(have_rows('project_house_tour')):?>
						<?php while( have_rows('project_house_tour')): the_row();?>
							<?php if(get_sub_field('project_house_tour_title')): ?>
								<h5 class="project-video-title"><?php the_sub_field('project_house_tour_title'); ?></h5>
							<?php endif; ?>	
							<?php if(get_sub_field('project_house_tour_platform')): ?>
								<?php $tour_platform = get_sub_field('project_house_tour_platform'); ?>
								<?php $tour_url = get_sub_field('project_house_tour_url'); ?>
								<?php if($tour_platform == 'cloudpano' && $tour_url): ?>
									<div id="<?php echo $tour_url; ?>">
										<script type="text/javascript" async data-short="<?php echo $tour_url; ?>" data-path="tours" data-is-self-hosted="undefined" width="100%" height="500px" src="https://app.cloudpano.com/public/shareScript.js"></script>
									</div>
								<?php elseif($tour_platform == 'teliportme' && $tour_url): ?>
									<iframe src="https://teliportme.com/embed/<?php echo $tour_url; ?>?ar=-3&amp;sfc=t&amp;lp=lt&amp;ls=d&amp;lz=50&amp;lo=-1" width="100%" height="500" frameborder="0" scrolling="no" allowfullscreen="allowfullscreen"></iframe>
								<?php endif; ?>
							<?php endif; ?>	
						<?php endwhile; ?>
					<?php endif; ?>	
				</div>
			<?php endif; ?>

			<!-- Ralated Tiles -->
			<?php if( $project_related_tile ): ?>
			<div class="project-tile-container project-container container">
				<div class="inner-container-heading">
					<h2>Featured Products</h2>
				</div>
				<div class="collection-tiles-list">
					<?php //loop tile
					foreach($project_related_tile as $tile): 
						$tile_link = get_permalink( $tile );
						$tile_title = get_the_title($tile); ?>
						<div class="single-tile-card">
							<a href="<?php echo $tile_link; ?>"><?php echo get_the_post_thumbnail( $tile, 'medium'); ?></a>
							<a href="<?php echo $tile_link; ?>"><h5><?php echo $tile_title; ?></h5></a>
						</div>
					<?php endforeach; 
					wp_reset_postdata();
					?>
				</div>			
			</div>
			<?php endif; ?>

			<!-- Testimonial -->
			<?php if( $project_testimonial ): ?>
			<div class="project-testimonial-container project-container container">
				<div class="inner-container-heading">
					<h2>Testimonial</h2>
				</div>

				<div class="swiper testimonial-tiles-list" id="testimonial-slider">
					<div class="swiper-wrapper">
						<?php foreach( $project_testimonial as $testimonial ): ?>
						<div class="swiper-slide testimonial-card">
							<p><?php echo $testimonial['project_testimonial_comment'] ?></p>
							<div class="testimonial-author">
								<img src="<?php echo $testimonial['project_testimonial_image'] ?>">
								<span><?php echo $testimonial['project_testimonial_name'] ?></span>
							</div>
						</div>
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
						<div class="swiper-scrollbar"></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

		<!--end content-->
		</main>

	</div><!--end container-->

</div><!-- close default .container_wrap element -->

<link type="text/css" rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/st-lightbox.css" id="st-lightbox-css">
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/st-lightbox.js" id="st-lightbox-js"></script>
<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/single-project.js"></script>

<?php get_footer();

