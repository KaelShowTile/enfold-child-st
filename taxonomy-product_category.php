<?php
	if( ! defined( 'ABSPATH' ) ){	die();	}

	global $avia_config;

	/**
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	get_header();

	do_action( 'ava_after_main_title' );

	/**
	 * @since 5.6.7
	 * @param string $main_class
	 * @param string $context					file name
	 * @return string
	 */
	$main_class = apply_filters( 'avf_custom_main_classes', 'av-main-' . basename( __FILE__, '.php' ), basename( __FILE__ ) );

	$term = get_queried_object();
	$total = get_total_collections();
	
	//get fields
	$related_projects = get_field('collection_category_related_project', $term->taxonomy . '_' . $term->term_id);
	$related_blog = get_field('collection_category_related_collection', $term->taxonomy . '_' . $term->term_id);

	?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' );?>'>

			<div class='container'>

				<main class='template-page template-collection content  <?php avia_layout_class( 'content' ); ?> units <?php echo $main_class; ?>' <?php avia_markup_helper( array( 'context' => 'content', 'post_type' => 'product' ) );?>>

                    <h1><?php single_term_title(); ?></h1>

					<!-- collection description -->
					<div class="collection-cate-container container">
						<div class="flex_column av_two_fifth  avia-builder-el-0  el_before_av_three_fifth  avia-builder-el-first  first flex_column_div  ">
							<div class="collection-cate-heading-group">
								<span><?php the_field('title_lable', $term->taxonomy . '_' . $term->term_id); ?></span>
								<h2><?php the_field('collection_description_title', $term->taxonomy . '_' . $term->term_id); ?></h2>
							</div>
							<div class="collection-cate-description-group">
								<p><?php the_field('collection_description_content', $term->taxonomy . '_' . $term->term_id); ?></p>
							</div>
						</div>
						<div class="flex_column av_three_fifth  avia-builder-el-1  el_after_av_two_fifth  avia-builder-el-last  flex_column_div  ">
							<?php echo wp_get_attachment_image( get_field('collection_category_description_image' , $term->taxonomy . '_' . $term->term_id), 'large');?>
						</div>
					</div>

					<!-- collection list -->
					<?php 
						echo get_collections_html(0, 12, null, true, 'collection-list-container container', 'load-more-btn');
					?>
					
					<!-- Related Project -->
					<?php if($related_projects): ?>
					<div class="related-project-container container">
						<h2><?php single_term_title(); ?> Projects</h2>
						<div class="home-other-project-inner">
							<div class="swiper" id="other-project-slider">
								<div class="swiper-wrapper">
								<?php foreach($related_projects as $project): ?>
									<?php $project_description = get_field( 'project_description', $project);
									if(strlen($project_description) > 120):
										$project_description = substr($project_description, 0, strrpos(substr($project_description, 0, 120), ' ')) . '...';
									endif; ?>
									<div class="swiper-slide project-card">
										<a href="<?php echo get_permalink( $project ) ?>"><?php echo get_the_post_thumbnail($project, 'project-vertical'); ?></a>
										<span><?php the_field( 'project_type', $project); ?></span>
										<a href="<?php echo get_permalink( $project ) ?>"><h5><?php echo get_the_title($project); ?></h5></a>
										<a href="<?php echo get_permalink( $project ) ?>"><p><?php echo $project_description; ?></p></a>
									</div>
								<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>
					
					<!-- Related Post -->
					<?php if($related_blog): ?>
					<div class="related-blog-container container">
						<h2>Get Inspired</h2>
						<div class="related-blog-container-inner">
						<?php foreach($related_blog as $blog): ?>
							<div class="blog-card">
								<a href="<?php echo get_permalink( $blog ) ?>"><?php echo get_the_post_thumbnail($blog, 'medium'); ?></a>
								<a href="<?php echo get_permalink( $blog ) ?>"><h5><?php echo get_the_title($blog); ?></h5></a>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>

				<!--end content-->
				</main>

			</div><!--end container-->

			<!-- Collection QA -->
			<?php if(have_rows('collection_category_qna', $term->taxonomy . '_' . $term->term_id)):?>
			<div class="collection-qa-container collection-category-qa-container">
				<div class="container">
					<div class="collection-qa-title-section">
						<h2>FAQs</h2>
					</div>
					<div class="accordion" id="collection-qa-accordion">
					<?php $qna_index = 0; ?>
					<?php while(have_rows('collection_category_qna', $term->taxonomy . '_' . $term->term_id)): the_row();?>
						<div class="accordion-item">
							<h5 class="accordion-header">
								<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collection-qna-<?php echo $qna_index; ?>" aria-expanded="true" aria-controls="collapseOne">
									<?php the_sub_field('collection_category_question'); ?>
								</button>
							</h5>
							<div id="collection-qna-<?php echo $qna_index; ?>" class="accordion-collapse collapse" data-bs-parent="#collection-qa-accordion">
								<div class="accordion-body">
									<?php the_sub_field('collection_category_answer'); ?>
								</div>
							</div>
						</div>
					<?php $qna_index++; ?>
					<?php endwhile; ?>
					</div>	
				</div>
			</div>
			<?php endif; ?>

		</div><!-- close default .container_wrap element -->

<?php get_footer();
