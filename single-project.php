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

?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container template-project template-single-project '>

		<main class='content units <?php avia_layout_class( 'content' ); ?> <?php echo avia_blog_class_string(); ?> <?php echo $main_class; ?>' <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'post' ) );?>>

			<h1 class="item-title"><?php the_title(); ?></h1>

			<!-- Project Video -->
			<?php if(get_field('project_video')): ?>
			<?php $video_shortcode = "[av_video src='" . get_field('project_video') . "' video_loop='aviaTBvideo_loop' video_controls='aviaTBvideo_controls' format='16-9' width='16' height='9' id='' sc_version='1.0']"; ?>

			<div class="project-video-container st_container_fullsize">
				<?php echo do_shortcode($video_shortcode); ?>
			</div>
			<?php endif;?>

			<!-- Project description -->
			<div class="project-description-container project-container container">
				<div class="flex_column av_one_half avia-builder-el-1 el_before_av_one_half avia-builder-el-first first flex_column_div">
					<?php the_field('project_description'); ?>
				</div>
				<div class="flex_column av_one_half avia-builder-el-2 el_after_av_one_half avia-builder-el-last flex_column_div">
					<table class="collection-description-container">
					<?php if(get_field('project_designer')): ?>
						<tr>
							<td class="attribute-name">Designer</td>
							<td><?php the_field('project_designer') ?></td>
						</tr>
					<?php endif; ?>
					<?php if(get_field('project_builder')): ?>
						<tr>
							<td class="attribute-name">Builder</td>
							<td><?php the_field('project_builder') ?></td>
						</tr>
					<?php endif; ?>
					<?php if(get_field('project_developer')): ?>
						<tr>
							<td class="attribute-name">Developer</td>
							<td><?php the_field('project_developer') ?></td>
						</tr>
					<?php endif; ?>
					<?php if(get_field('project_architect')): ?>
						<tr>
							<td class="attribute-name">Architect</td>
							<td><?php the_field('project_architect') ?></td>
						</tr>
					<?php endif; ?>
					<?php if(get_field('project_stylist')): ?>
						<tr>
							<td class="attribute-name">Stylist</td>
							<td><?php the_field('project_stylist') ?></td>
						</tr>
					<?php endif; ?>
					</table>
				</div>
			</div>
				
			<!-- Project gallery -->
			<div class="project-gallery-container project-container container">
				<div class="inner-container-heading">
					<h2>Photo Gallery</h2>
				</div>
				<?php if( $project_images ): ?>
				<div class="swiper" id="tile-gallery">
					<div class="swiper-wrapper">
						<?php foreach($project_images as $image_id): ?>
						<div class="swiper-slide"><?php echo wp_get_attachment_image( $image_id, 'large'); ?></div>
						<?php endforeach; ?>
					</div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
					<div class="swiper-scrollbar"></div>
				</div>
				<?php endif; ?>
			</div>

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
					$tile_title = get_the_title($tile);
					$tile_finish = get_field('tile_finish', $tile);
					//loop finish
					if($tile_finish):
						foreach($tile_finish as $finish): ?>
							<div class="single-tile-card">
								<a href="<?php echo $tile_link; ?>"><?php echo wp_get_attachment_image( $finish['finish_image'], 'medium'); ?></a>
								<a href="<?php echo $tile_link; ?>"><h5><?php echo $tile_title . " " . $finish['finish_name']; ?></h5></a>
								<p><?php echo $finish['product_code']; ?></p>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				</div>
				
			</div>
			<?php endif; ?>

			<!-- House Tour -->
			<?php if( get_field('project_house_tour') ): ?>
				<div class="project-house-tour-container container_wrap fullsize">
					<h2>House Tour</h2>
					<iframe src="<?php echo get_field('project_house_tour')?>" width="100%" height="600px"></iframe>
				</div>
			<?php endif; ?>

		<!--end content-->
		</main>

	</div><!--end container-->

</div><!-- close default .container_wrap element -->

<?php get_footer();

