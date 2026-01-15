<?php
	if( ! defined( 'ABSPATH' ) )	{ die(); }

	global $avia_config;

	/**
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	get_header();
    $tile_id = get_the_ID();
	$related_project_ids = get_post_meta($tile_id, 'related_project', true);

	$title = __( 'Tile', 'avia_framework' ); //default blog title
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

	//render slider content
	$tile_images = get_field('tile_photo_gallery');
	$tile_video = get_field('tile_video');
	$tile_video_url = ""; 
	$tile_video_thumb_url = ""; //setup default url later in case My or Naomi forget to upload thumbnail
	$tile_slider_output = "";

	if($tile_video){
		$tile_video_url = $tile_video['video_url'];
		$tile_video_thumb_id = $tile_video['video_thumbnail'];
		$tile_slider_output .= '<div class="swiper-slide"><a class="noLightbox st-lightbox" href="' . $tile_video_url . '"><img src="' . get_stylesheet_directory_uri() . '/assets/img/play-button.svg" class="video-play-button"></a>' . wp_get_attachment_image( $tile_video_thumb_id, 'full') . '</div>';
	}

	if($tile_images){
		foreach($tile_images as $image_id){
			$tile_slider_output .= '<div class="swiper-slide"><a href="'. wp_get_attachment_image_url( $image_id, 'full') .'">' . wp_get_attachment_image( $image_id, 'full') . '</a></div>';
		}
	}
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container template-tile template-single-tile '>

		<main class='content units <?php avia_layout_class( 'content' ); ?> <?php echo avia_blog_class_string(); ?> <?php echo $main_class; ?>' <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'tile' ) );?>>

			<h1><?php the_title(); ?></h1>

			<div class="tile-sidebar">
				<!-- Load finish-->
				<?php if( have_rows('tile_finish') ): ?>
					<div class="accordion" id="tile-finish-accordion">
					<?php while( have_rows('tile_finish') ) : the_row();?>
						<div class="accordion-item">
							<?php $finishName = get_sub_field('finish_name'); ?>
							<h2 class="accordion-header">
							<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $finishName; ?>" aria-expanded="true" aria-controls="collapseOne">
								<!-- Load finish name-->
								<h5><?php echo $finishName; ?><br><span>Code: <?php the_sub_field('product_code'); ?></span></h5>
							</button>
							</h2>
							<div id="<?php echo $finishName; ?>" class="accordion-collapse collapse show" data-bs-parent="#tile-finish-accordion">
								<div class="accordion-body">
									<!-- Load finish image-->
									<?php $finishImageID = get_sub_field('finish_image'); ?>
									<a class="Lightbox" href="<?php echo wp_get_attachment_image_url( $finishImageID, 'full'); ?>">
										<img src="<?php echo wp_get_attachment_image_url( $finishImageID, 'medium'); ?>">
									</a>
									<!-- Load sizes-->
									<?php if( have_rows('tile_size') ): ?>
										<ul class="tile-size-list">
										<?php while( have_rows('tile_size') ) : the_row(); ?>
											<li>
												<span><?php the_sub_field('tile_size_name'); ?></span>
												<a id="add-to-basket" data-product-name="<?php echo the_title() . ' - ' . $finishName . ' - ' . get_sub_field('tile_size_name') ; ?>" data-tile-name="<?php echo the_title(); ?>" data-product-finish="<?php echo $finishName; ?>" data-product-size="<?php echo get_sub_field('tile_size_name'); ?>" data-product-image_id="<?php echo $finishImageID; ?>" data-product-image_url="<?php echo wp_get_attachment_image_url($finishImageID, 'medium'); ?>">Add to Idea Basket</a>
											</li>
										<?php endwhile; ?>
										</ul>
									<?php endif ?>
									<!-- btns-->
									<div class="tile-sidebar-btns">
										
										<?php if(get_sub_field('visual_theatre')): ?>
											<a href="<?php the_sub_field('visual_theatre'); ?>">Virtual Theatre</a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endwhile;?>
					</div>
				<?php endif;?>
			</div>

			<div class="tile-details">
				<?php //Tile gallery
					if( $tile_images || $tile_video):
				?>
						<div class="swiper" id="tile-gallery">
							<div class="swiper-wrapper">
								<?php echo $tile_slider_output; ?>
							</div>

							<div class="swiper-button-prev"></div>
							<div class="swiper-button-next"></div>
							<div class="swiper-scrollbar"></div>

						</div>
					<?php endif; ?>
				<!-- Tile metas-->		
				<div class="tile-decripton">
					<?php the_field('tile_description'); ?>
					<div class="collection-description-container">
							
						<?php if(get_field('indent_item')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Indent Item</p>
								<p>Yes</p>
							</div>
						<?php endif; ?>
					
						<?php if(get_field('tile_design')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Design</p>
								<p><?php the_field('tile_design'); ?></p>
							</div>
						<?php endif; ?>

						<?php if(get_field('tile_material')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Material</p>
								<p><?php the_field('tile_material'); ?></p>
							</div>
						<?php endif; ?>

						<?php if(get_field('tile_application')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Application</p>
								<p><?php the_field('tile_application'); ?></p>
							</div>
						<?php endif; ?>

						<?php if(get_field('tile_variation')): ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Variation</p>
								<p><?php the_field('tile_variation'); ?></p>
							</div>
						<?php endif; ?>
						
					</div>
				</div>

				<!-- Related project-->	
				<?php if($related_project_ids):?>
				<div class="single-tile-related-project-container">
					<div class="inner-container-heading">
						<h2>Related Project</h2>
					</div>
					<div class="collection-project-list">
					<?php foreach($related_project_ids as $project): ?>
						<div class="single-project-card single-project-card-container">
							<a href="<?php echo get_permalink($project); ?>"><?php echo get_the_post_thumbnail($project, 'project-vertical'); ?></a>
							<span><?php the_field('project_type', $project); ?></span>
							<a href="<?php echo get_permalink($project); ?>"><h5><?php echo get_the_title($project); ?></h5></a>
							<p><?php echo stCutText(get_field('project_description', $project));?></p>
						</div>
					<?php endforeach; ?>
					</div>
				</div>
				<?php endif;?>
				</div>

		<!--end content-->
		</main>

	</div><!--end container-->

</div><!-- close default .container_wrap element -->

<link type="text/css" rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/st-lightbox.css" id="st-lightbox-css">
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/st-lightbox.js" id="st-lightbox-js"></script>

<?php get_footer();
