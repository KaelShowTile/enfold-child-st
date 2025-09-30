<?php
	if( ! defined( 'ABSPATH' ) )	{ die(); }

	global $avia_config;

	/**
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	get_header();
    $collection_id = get_the_ID();

	$title = __( 'Collection', 'avia_framework' ); //default blog title
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

	$collection_tiles = get_field('tiles_in_collection');

	$collection_gallery = [];
	$collection_design = [];
	$collection_material = [];
	$collection_application = [];
	$collection_variation = [];
	$collection_finish = [];//get all finish-size pair
	$grouped_finish = [];//combine $collection_finish by finish name & merge size
	$collection_tiles_list = [];

	if($collection_tiles){
		foreach($collection_tiles as $tile){
			//get ACF value of each tile
			$tile_images = get_field('tile_photo_gallery', $tile);//return image ids
			$tile_design = get_field('tile_design', $tile);
			$tile_material = get_field('tile_material', $tile);
			$tile_application = get_field('tile_application', $tile);
			$tile_variation = get_field('tile_variation', $tile);
			$tile_finish = get_field('tile_finish', $tile);//repeater field

			//combine iamges
			$collection_gallery = array_unique(array_merge($collection_gallery, $tile_images));

			//add tile meta to project meta
			if (!in_array($tile_design, $collection_design)) {
				$collection_design[] = $tile_design;
			}

			if (!in_array($tile_material, $collection_material)) {
				$collection_material[] = $tile_material;
			}

			if (!in_array($tile_application, $collection_application)) {
				$collection_application[] = $tile_application;
			}

			if (!in_array($tile_variation, $collection_variation)) {
				$collection_variation[] = $tile_variation;
			}

			//get title & link
			$tile_title = get_the_title($tile);
			$permalink = get_permalink( $tile );

			//get first image as thumbnail
			$tile_thumb;
			if($tile_images){
				$tile_thumb = wp_get_attachment_image_url( $tile_images[0], 'medium' );
			}else{
				$tile_thumb = get_stylesheet_directory_uri() . '/assets/img/st-place-holder.jpg';
			}

			//get indent
			$tile_indent = false;
			if(get_field('indent_item', $tile)){
				$tile_indent = true;
			}

			//generate finish-size string
			$total_finish = 0;
			$total_size = 0;
			
			if(get_field('tile_finish', $tile)){
				while( the_repeater_field('tile_finish', $tile) ){
					$finish_name = get_sub_field('finish_name');
					$total_finish++;
					//get sizes
					if(get_sub_field('tile_size')){
						while( the_repeater_field('tile_size', $tile) ){
							$finish_size = get_sub_field('tile_size_name');
							//put finish name pair with size into array
							$collection_finish[] =  ['finish_name' => $finish_name, 'finish_size' => $finish_size];
							$total_size++;
						}
					}
				}
			}

			$collection_tiles_list[] = ['tile_title' => $tile_title, 'title_link' => $permalink, 'title_thumb_url' => $tile_thumb, 'total_finish' => $total_finish, 'total_size' => $total_size];
		}
	}

	$unique_finish = [];
	foreach ($collection_finish as $item) {
		$name = $item['finish_name'];
		$size = $item['finish_size'];
		
		// Initialize group if not exists
		if (!isset($unique_finish[$name])) {
			$unique_finish[$name] = [];
		}
		
		// Use finish_size as key to avoid duplicates
		$unique_finish[$name][$size] = true;
	}

	foreach ($unique_finish as $name => $sizes) {
    $uniqueSizes = array_keys($sizes);
    sort($uniqueSizes); // Optional: sort sizes for consistency
    $grouped_finish[] = [
        'finish_name' => $name,
        'finish_size' => implode(', ', $uniqueSizes)
    ];

	$collection_projects = get_field('related_project');
}

?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container template-collection template-single-collection '>

		<main class='content units <?php avia_layout_class( 'content' ); ?> <?php echo avia_blog_class_string(); ?> <?php echo $main_class; ?>' <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'post' ) );?>>

			<h1><?php the_title(); ?></h1>

			<!-- Image gallery -->
			<?php if($collection_gallery): ?>
			<div class="collection-gallery-slider collection-container container">
				<div class="swiper" id="tile-gallery">
					<div class="swiper-wrapper">
						<?php foreach($collection_gallery as $image_id): ?>
						<div class="swiper-slide"><?php echo wp_get_attachment_image( $image_id, 'large' ); ?></div>
						<?php endforeach; ?>
					</div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
					<div class="swiper-scrollbar"></div>
				</div>
			</div>
			<?php endif; ?>

			<!-- Description & metas -->
			<div class="collection-description-container collection-container container">
				<div class="flex_column av_one_half avia-builder-el-1 el_before_av_one_half avia-builder-el-first first flex_column_div">
					<?php the_field('collection_description'); ?>
				</div>
				<div class="flex_column av_one_half avia-builder-el-2 el_after_av_one_half avia-builder-el-last flex_column_div">
					<table>
						<?php if (!empty($collection_design)): 
						$design_string = implode(", ", $collection_design); ?>
						<tr>
							<td>Design</td>
							<td><?php echo $design_string; ?></td>
						</tr>
						<?php endif; ?>

						<?php if (!empty($collection_material)): 
						$material_string = implode(", ", $collection_material); ?>
						<tr>
							<td>Material</td>
							<td><?php echo $material_string; ?></td>
						</tr>
						<?php endif; ?>

						<?php if (!empty($collection_application)): 
						$application_string = implode(", ", $collection_application); ?>
						<tr>
							<td>Application</td>
							<td><?php echo $application_string; ?></td>
						</tr>
						<?php endif; ?>

						<?php if (!empty($collection_variation)): 
						$variation_string = implode(", ", $collection_variation); ?>
						<tr>
							<td>Variation</td>
							<td><?php echo $variation_string; ?></td>
						</tr>
						<?php endif; ?>

						<?php if (!empty($grouped_finish)): ?>
						<tr>
							<td>Finish | Size</td>
							<td>
								<?php foreach($grouped_finish as $finish): ?>
									<p><?php echo $finish['finish_name']; ?> | <?php echo $finish['finish_size']; ?></p>
								<?php endforeach; ?>
							</td>
						</tr>
						<?php endif; ?>

					</table>
				</div>
			</div>
			
			<!-- Colour/tile in collection -->
			<div class="collection-tiles-container collection-container container">
				<div class="inner-container-heading">
					<h2>Colours</h2>
				</div>
				<?php foreach($collection_tiles_list as $tile): ?>
					<a href="<?php echo $tile['title_link']; ?>">
						<div class="single-tile-card">
							<img src="<?php echo $tile['title_thumb_url']; ?>">
							<div class="tile-card-detail">
								<h5><?php echo $tile['tile_title']; ?></h5>
								<p><?php echo $tile['total_finish']; ?> finishes | <?php echo $tile['total_size']; ?> sizes</p>
							</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
			
			<!-- Related project -->
			<?php if($collection_projects):?>
			<div class="collection-tile-container collection-container container">
				<div class="inner-container-heading">
					<h2>Product</h2>
				</div>

				<?php foreach($collection_projects as $project): ?>
				</div class="single-project-card">
					<?php echo wp_get_attachment_image(get_field('project_photos', $project)[0], 'medium' ); ?>
					<h5><?php echo get_the_title($project); ?></h5>
					<?php $project_des = stCutText(get_field('project_description', $project));?>
					<p><?php echo $project_des; ?></p>
				</div>
				<?php endforeach; ?>

			</div>
			<?php endif;?>

			<!-- Collection QA -->
			<?php if(have_rows('collection_qna')):?>
			<div class="collection-qa-container collection-container container">
				<div class="accordion" id="collection-qa-accordion">
				<?php $qna_index = 0; ?>
				<?php while( have_rows('collection_qna')): the_row();?>
					<div class="accordion-item">
						<h5 class="accordion-header">
							<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collection-qna-<?php echo $qna_index; ?>" aria-expanded="true" aria-controls="collapseOne">
								<?php the_sub_field('collection_qna_question'); ?>
							</button>
						</h5>
						<div id="collection-qna-<?php echo $qna_index; ?>" class="accordion-collapse collapse" data-bs-parent="#collection-qa-accordion">
							<div class="accordion-body">
								<?php the_sub_field('collection_qna_answer'); ?>
							</div>
						</div>
					</div>
				<?php $qna_index++; ?>
				<?php endwhile; ?>
				</div>				
			</div>
			<?php endif; ?>

		</main>

	</div><!--end container-->

</div><!-- close default .container_wrap element -->

<?php get_footer();
