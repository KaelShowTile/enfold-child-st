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

	$catalogue_pdf_url = get_field('catalogue_pdf');
	$catalogue_pdf_shortcode = "";
	if($catalogue_pdf_url){
		$catalogue_pdf_shortcode = '[dflip source="' . esc_url($catalogue_pdf_url) . '" viewertype="flipbook" is3d="true" height="750px"][/dflip]';
	}
	

	$collection_tiles = get_field('tiles_in_collection');

	$collection_gallery = [];
	$collection_video = [];
	$collection_design = [];
	$collection_material = [];
	$collection_application = [];
	$collection_variation = [];
	$collection_finish = [];//get all finish-size pair
	$grouped_finish = [];//combine $collection_finish by finish name & merge size
	$collection_tiles_list = [];
	$collection_projects = [];

	if($collection_tiles){
		foreach($collection_tiles as $tile){
			//get ACF value of each tile
			$tile_images = get_field('tile_photo_gallery', $tile);//return image ids
			$tile_video = get_field('tile_video', $tile);
			$tile_design = get_field('tile_design', $tile);
			$tile_material = get_field('tile_material', $tile);
			$tile_application = get_field('tile_application', $tile);
			$tile_variation = get_field('tile_variation', $tile);
			$tile_finish = get_field('tile_finish', $tile);//repeater field

			//get title & link
			$tile_title = get_the_title($tile);
			$permalink = get_permalink($tile);

			//get related project ids
			$collection_projects_id = get_post_meta($tile, 'related_project', true);
			if($collection_projects_id){
				$collection_projects = array_unique(array_merge($collection_projects, $collection_projects_id));
			}
			
			//combine images with tile title
			if($tile_images){
				if(count($tile_images) > 3){
					$tile_images = array_slice($tile_images, 0, 3);
				}
				foreach($tile_images as $image_id){
					$collection_gallery[] = ['image_id' => $image_id, 'tile_title' => $tile_title];
				}
			}

			//combine videos
			if($tile_video){
				$tile_video_url = $tile_video['video_url'];
				$tile_video_thumb_id = $tile_video['video_thumbnail'];
				$collection_video[] = ['thumbnail_id' => $tile_video_thumb_id, 'video_url' => $tile_video_url];
			}
			
			//add tile meta to project meta
			if (!empty($tile_design && !in_array($tile_design, $collection_design))) {
				$collection_design[] = $tile_design;
			}

			if (!empty($tile_material && !in_array($tile_material, $collection_material))) {
				$collection_material[] = $tile_material;
			}

			if (!empty($tile_application) && !in_array($tile_application, $collection_application)) {
				$collection_application[] = $tile_application;
			}

			if (!empty($tile_variation && !in_array($tile_variation, $collection_variation))) {
				$collection_variation[] = $tile_variation;
			}

			//get thumbnail
			$tile_thumb;
			$medium_url = get_the_post_thumbnail_url($tile, 'medium' );
			$full_url = get_the_post_thumbnail_url($tile, 'full' );
			$tile_thumb = ($medium_url !== $full_url) ? $medium_url : $full_url;
			if(!$tile_thumb){
				$tile_thumb = get_stylesheet_directory_uri() . '/assets/img/st-place-holder.jpg';
			}

			//get indent
			$tile_indent = false;
			if(get_field('indent_item', $tile)){
				$tile_indent = true;
			}

			//generate finish-size string
			$total_finish = 0;
			$total_finish_suffix = "finish";
			$total_size = 0;
			$total_size_suffix = "size";
			
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
				if($total_finish > 1){
					$total_finish_suffix = "finishes";
				}
				if($total_size > 1){
					$total_size_suffix = "sizes";
				}
			}

			$collection_tiles_list[] = ['tile_title' => $tile_title, 'title_link' => $permalink, 'title_thumb_url' => $tile_thumb, 'total_finish' => $total_finish, 'total_size' => $total_size, 'total_finish_suffix' => $total_finish_suffix, 'total_size_suffix' => $total_size_suffix ];
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

	//Render slider
	$collection_slider_output = "";
	if($collection_gallery || $collection_video){
		if($collection_video){
			foreach($collection_video as $video_item){
				$collection_slider_output .= '<div class="swiper-slide"><a class="noLightbox st-lightbox" href="' . $video_item['video_url'] . '"><img src="' . get_stylesheet_directory_uri() . '/assets/img/play-button.svg" class="video-play-button"></a>' . wp_get_attachment_image(  $video_item['thumbnail_id'], 'full') . '</div>';
			}
		}
		
		if($collection_gallery){
			foreach($collection_gallery as $gallery_item){
				$collection_slider_output .= '<div class="swiper-slide"><a href="'. wp_get_attachment_image_url($gallery_item['image_id'], 'full') .'">' . wp_get_attachment_image($gallery_item['image_id'], 'full' ) . '</a><p class="tile-name">' . esc_html($gallery_item['tile_title']) . '</p></div>';
			}	
		}
	}
}

?>

<script src="<?php echo get_stylesheet_directory_uri();?>/assets/js/single-collection.js"></script>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container template-collection template-single-collection '>

		<main class='content units <?php avia_layout_class( 'content' ); ?> <?php echo avia_blog_class_string(); ?> <?php echo $main_class; ?>' <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'post' ) );?>>

			<h1 class="item-title"><?php the_title(); ?></h1>

			<!-- Image gallery -->
			<?php if($collection_gallery): ?>
			<div class="collection-gallery-slider collection-container">
				<div class="swiper" id="tile-gallery">
					<div class="swiper-wrapper">
						<?php echo $collection_slider_output; ?>
					</div>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
					<div class="swiper-scrollbar"></div>
				</div>
			</div>
			<?php endif; ?>

			<!-- Description & metas -->
			<div class="collection-description-outer-container collection-container">
				<div class="flex_column av_one_half avia-builder-el-1 el_before_av_one_half avia-builder-el-first first flex_column_div">
					<?php the_field('collection_description'); ?>
				</div>
				<div class="flex_column av_one_half avia-builder-el-2 el_after_av_one_half avia-builder-el-last flex_column_div">
					<div class="collection-description-container">
						<?php if (!empty($collection_design)): 
							$design_string = implode(", ", $collection_design); ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Design</p>
								<p><?php echo $design_string; ?></p>
							</div>
						<?php endif; ?>

						<?php if (!empty($collection_material)): 
							$material_string = implode(", ", $collection_material); ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Material</p>
								<p><?php echo $material_string; ?></p>
							</div>
						<?php endif; ?>

						<?php if (!empty($collection_application)): 
							$application_string = implode(", ", $collection_application); ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Application</p>
								<p><?php echo $application_string; ?></p>
							</div>
						<?php endif; ?>

						<?php if (!empty($collection_variation)): 
							$variation_string = implode(", ", $collection_variation); ?>
							<div class="description-meta-col half-col">
								<p class="attribute-name">Variation</p>
								<p><?php echo $variation_string; ?></p>
							</div>
						<?php endif; ?>

						<?php if (!empty($grouped_finish)): ?>
							<div class="description-meta-col full-col">
								<p class="attribute-name">Finish | Size</p>
								<?php foreach($grouped_finish as $finish): ?>
									<p><?php echo $finish['finish_name']; ?> | <?php echo $finish['finish_size']; ?></p>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
			
			<!-- Colour/tile in collection -->
			<div class="collection-tiles-container collection-container">
				<div class="inner-container-heading">
					<h2>Colours</h2>
				</div>
				<div class="collection-tiles-list">
				<?php foreach($collection_tiles_list as $tile): ?>
					<div class="single-tile-card">
						<a href="<?php echo $tile['title_link']; ?>"><img src="<?php echo $tile['title_thumb_url']; ?>"></a>
						<div class="tile-card-detail">
							<a href="<?php echo $tile['title_link']; ?>"><h5><?php echo $tile['tile_title']; ?></h5></a>
							<p><?php echo $tile['total_finish'] . ' ' . $tile['total_finish_suffix'] . ' | ' . $tile['total_size'] . ' ' . $tile['total_size_suffix']; ?></p>
						</div>
					</div>		
				<?php endforeach; ?>
				</div>
			</div>

			<?php if(get_field('collection_video')): ?>
			<div class="collection-tile-container collection-container collection-video-container">
				<?php $video_shortcode = "[av_video src='" . get_field('collection_video') . "' video_loop='aviaTBvideo_loop' video_controls='aviaTBvideo_controls' format='16-9' width='16' height='9' id='' sc_version='1.0']"; ?>
				<?php echo do_shortcode($video_shortcode); ?>
			</div>
			<?php endif; ?>	
			
			<!-- Related project -->
			<?php if($collection_projects):?>
			<div class="collection-tile-container collection-container">
				<div class="inner-container-heading">
					<h2><?php the_title(); ?> Project</h2>
				</div>
				<div class="collection-project-list">
				<?php foreach($collection_projects as $project): ?>
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

			<!-- Collection Catalogue -->
			 <?php if($catalogue_pdf_url): ?>
			<div class="collection-pdf-container collection-container">
				<a id="collection-pdf-btn" class="st-link-button small-style desktop-only">Show Catalogue of <?php the_title(); ?></a>
				<div id="collection-pdf-content" style="display:none;">				
					<?php echo do_shortcode($catalogue_pdf_shortcode); ?>				
				</div>
				<a href="<?php echo $catalogue_pdf_url; ?>" class="st-link-button small-style mobile-only">Open Catalogue of <?php the_title(); ?></a>
			</div>
			<?php endif; ?>

			<!-- Collection QA -->
			<?php if(have_rows('collection_qna')):?>
			<div class="collection-qa-container collection-container">
				<div class="inner-container-heading">
					<h2>FAQ</h2>
				</div>
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

<link type="text/css" rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/st-lightbox.css" id="st-lightbox-css">
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/st-lightbox.js" id="st-lightbox-js"></script>

<?php get_footer();
