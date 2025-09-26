<?php
	if( ! defined( 'ABSPATH' ) )	{ die(); }

	global $avia_config;

	/**
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	get_header();

    $tile_id = get_the_ID();

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
								<h5><?php echo $finishName; ?></h5>
								<!-- Load product code-->
								<span><?php the_sub_field('product_code'); ?></span>
							</button>
							</h2>
							<div id="<?php echo $finishName; ?>" class="accordion-collapse collapse show" data-bs-parent="#tile-finish-accordion">
								<div class="accordion-body">
									<!-- Load finish image-->
									<?php $finishImageID = get_sub_field('finish_image'); 
									$imageSize = 'medium'; ?>
									<?php echo wp_get_attachment_image( $finishImageID, $imageSize); ?>
									<!-- Load sizes-->
									<?php if( have_rows('tile_size') ): ?>
										<ul class="tile-size-list">
										<?php while( have_rows('tile_size') ) : the_row(); ?>
											<li><?php the_sub_field('tile_size_name'); ?></li>
										<?php endwhile; ?>
										</ul>
									<?php endif ?>
									<!-- btns-->
									<div class="tile-sidebar-btns">
										<a id="add-to-basket" data-product-id="<?php echo $tile_id ?>" data-product-finish="<?php echo $finishName; ?>">Add to Idea Basket</a>
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
					$tile_images = get_field('tile_photo_gallery');
					$image_size = 'large';
					if( $tile_images ):
				?>
				<div class="swiper" id="tile-gallery">
					<div class="swiper-wrapper">
						<?php foreach($tile_images as $image_id): ?>
						<div class="swiper-slide"><?php echo wp_get_attachment_image( $image_id, $image_size ); ?></div>
						<?php endforeach; ?>
					</div>

					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
					<div class="swiper-scrollbar"></div>

				</div>
				<?php endif; ?>
				<!-- Tile metas-->		
				<div class="tile-decripton">
					<?php the_field('tile_description'); ?>
					<table class="tile-attributes">
						<tr>
							<td>Design</td>
							<td><?php the_field('tile_design'); ?></td>
						</tr>
						<tr>
							<td>Material</td>
							<td><?php the_field('tile_material'); ?></td>
						</tr>
						<tr>
							<td>Application</td>
							<td><?php the_field('tile_application'); ?></td>
						</tr>
						<tr>
							<td>variation</td>
							<td><?php the_field('tile_variation'); ?></td>
						</tr>
						<tr>
							<td>Faces</td>
							<td><?php the_field('tile_faces'); ?></td>
						</tr>
					</table>
				</div>
			</div>

		<!--end content-->
		</main>

	</div><!--end container-->

</div><!-- close default .container_wrap element -->

<?php
		get_footer();

