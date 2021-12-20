
<?php

/**
 * The template for displaying the Event Index
 *
 * @package PSN WordPress Theme
 * @version 1.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

get_header(); ?>

<?php nectar_page_header(get_option('page_for_posts')); ?>

<div class="container-wrap">

	<div class="container main-content">

		<div class="row">
			<form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" method="POST" id="filter">
				<div class="col span_2">
					<input type="text" class="search-field" placeholder="Search for events ..." value="" name="search" title="Search for:">
				</div>
				<?php

				$category_posts = get_posts( array(  'post_type' => 'category', 'post_status' => 'publish', 'numberposts'  => 30) );

				if ($category_posts) :
					echo '<div class="col span_3">';
					echo '<select name="categoryfilter"><option value="">Select community...</option>';
					foreach( $category_posts as $post ): 
						if(isset($_GET['categoryfilter']) && $_GET['categoryfilter'] != "" && $_GET['categoryfilter'] == get_the_ID()){
							echo '<option value="' . get_the_ID() . '" selected >' . get_the_title() . '</option>'; // ID of the category as an option value
						}else{
							echo '<option value="' . get_the_ID() . '">' . get_the_title() . '</option>'; // ID of the category as an option value
						}

					endforeach;
					echo '</select>';
					echo '</div>';
					wp_reset_postdata(); 
				endif;

				if ($country_field = get_field_object('country')) :
					echo '<div class="col span_3">';
					echo '<select name="countryfilter"><option value="">Select region...</option>';
					foreach ($country_field['choices'] as $key => $value) :
						if(isset($_GET['countryfilter']) && $_GET['countryfilter'] != "" && $_GET['countryfilter'] ==  $key ){
							echo '<option value="' . $key . '" selected >' . $value . '</option>'; // ID of the category as an option value
						}else{
							echo '<option value="' . $key . '">' . $value . '</option>'; // ID of the category as an option value
						}
					endforeach;
					echo '</select>';
					echo '</div>';
				endif;

				

				// if ($label_field = get_field_object('label_event')) :
				// 	echo '<div class="col span_2">';
				// 	echo '<select name="labelfilter"><option value="">Select type...</option>';
				// 	foreach ($label_field['choices'] as $key => $value) :
				// 		echo '<option value="' . $key . '">' . $value . '</option>'; // ID of the category as an option value
				// 	endforeach;
				// 	echo '</select>';
				// 	echo '</div>';
				// elseif 
				
				if ($label_field = get_field_object('label_event')) :
					echo '<div class="col span_3">';
					echo '<select name="labelfilter"><option value="">Select type...</option>';
					foreach ($label_field['choices'] as $key => $value) :
						echo '<option value="' . $key . '">' . $value . '</option>'; // ID of the category as an option value
					endforeach;
					echo '</select>';
					echo '</div>';
				else :
					echo '<div class="col span_3">';
					echo '<select name="labelfilter"><option value="">Select type...</option>';
					// foreach ($label_field['choices'] as $key => $value) :
						echo '<option value="Showcase">Showcase</option>'; // ID of the category as an option value
						echo '<option value="Roadshow">Roadshow</option>'; // ID of the category as an option value
						echo '<option value="Virtual">Virtual</option>'; // ID of the category as an option value
						echo '<option value="Webinar">Webinar</option>'; // ID of the category as an option valur
					// endforeach;
					echo '</select>';
					echo '</div>';
				endif;	?>

				<?php echo '<div class="col span_1">'; ?>
				<input class="nectar-button regular  regular-button" style="line-height: 11px;" type="submit" value="Filter">
				<input type="hidden" name="action" value="myfilter">
				<input type="hidden" name="post_type" value="event">
				<?php echo '</div>'; ?>
			</form>
			<div id="response"></div>
		</div><!-- Row End -->
		<!-- script -->
		<script>
			jQuery(function($) {

				// Filter Insights
				$('#filter').submit(function() {
					history.pushState('', 'title', '');
					var filter = $('#filter');

					$.ajaxSetup({cache: false});

					$.ajax({
						url: filter.attr('action'),
						data: filter.serialize(), // form data
						type: filter.attr('method'), // POST
						beforeSend: function(xhr) {
							filter.find('button').text('Processing...'); // changing the button label
						},
						success: function(data) {
							filter.find('button').text('Submit'); // changing the button label back
							$('#filter-container').empty(); // Remove existing data
							$('#filter-container').html(data); // insert data
							history.pushState('', 'title', '?' + $('#filter').serialize());
							//history.pushState({}, null, newUrl);
						}
					});
					return false;
				});


				// Pagination
				// we will remove the button and load its new copy with AJAX, that's why $('body').on()
				/*
				 * Load More
				 */
				$('body').on('click', '#psn_loadmore', function() {

					var country = jQuery('#filter').find('select[name="countryfilter"]').val();
					var postType = jQuery('#filter').find('input[name="post_type"]').val();
					var category = jQuery('#filter').find('select[name="categoryfilter"]').val(); 
					// var readTime = jQuery('#filter').find('select[name="readtimefilter"]').val();
					var label = jQuery('#filter').find('select[name="labelfilter"]').val();
					$.ajax({
						url: psn_loadmore_params.ajaxurl, // AJAX handler
						data: {
							'action': 'loadmore', // the parameter for admin-ajax.php
							'query': psn_loadmore_params.posts, // loop parameters passed by wp_localize_script()
							'page': psn_loadmore_params.current_page, // current page
							'first_page': psn_loadmore_params.first_page,
							'countryfilter': country,
							'post_type':postType,
							'categoryfilter': category,
							'labelfilter' : label
						},
						type: 'POST',
						beforeSend: function(xhr) {
							$('#psn_loadmore').text('Loading...'); // some type of preloader
						},
						success: function(data) {

							$('#filter-container').append(data);
							//$('#psn_loadmore').remove();
							//$('#psn_pagination').before(data).remove();
							$('#psn_loadmore').text('More Events'); // some type of preloader
							psn_loadmore_params.current_page++;


						}
					});
					return false;
				});
			});
		</script>
		<style>
			@media only screen and (min-width: 690px){
				.post-header{
				max-height: 81px;
				min-height: 80px;
				overflow: hidden;
				}
			}
			@media only screen and (max-width: 1300px) and (min-width: 1024px){
				.post-header {
					max-height: 90px;
					min-height: auto;
					overflow: hidden;
				}
			}
		</style>
		<!-- Script End -->


		<div class="row">

			<?php
			$nectar_options = get_nectar_theme_options();

			$blog_type = $nectar_options['blog_type'];
			if ($blog_type === null) {
				$blog_type = 'std-blog-sidebar';
			}

			$masonry_class         = null;
			$masonry_style         = null;
			$masonry_style_parsed  = null;
			$standard_style_parsed = null;
			$infinite_scroll_class = null;
			$load_in_animation     = (!empty($nectar_options['blog_loading_animation'])) ? $nectar_options['blog_loading_animation'] : 'none';
			$blog_standard_type    = (!empty($nectar_options['blog_standard_type'])) ? $nectar_options['blog_standard_type'] : 'classic';
			$enable_ss             = (!empty($nectar_options['blog_enable_ss'])) ? $nectar_options['blog_enable_ss'] : 'false';
			$auto_masonry_spacing  = (!empty($nectar_options['blog_auto_masonry_spacing'])) ? $nectar_options['blog_auto_masonry_spacing'] : '4px';

			$remove_post_date           = (!empty($nectar_options['blog_remove_post_date'])) ? $nectar_options['blog_remove_post_date'] : '0';
			$remove_post_author         = (!empty($nectar_options['blog_remove_post_author'])) ? $nectar_options['blog_remove_post_author'] : '0';
			$remove_post_comment_number = (!empty($nectar_options['blog_remove_post_comment_number'])) ? $nectar_options['blog_remove_post_comment_number'] : '0';
			$remove_post_nectar_love    = (!empty($nectar_options['blog_remove_post_nectar_love'])) ? $nectar_options['blog_remove_post_nectar_love'] : '0';

			// Enqueue masonry script if selected.
			if (
				$blog_type === 'masonry-blog-sidebar' ||
				$blog_type === 'masonry-blog-fullwidth' ||
				$blog_type === 'masonry-blog-full-screen-width'
			) {
				$masonry_class = 'masonry';
			}

			$blog_masonry_style = (!empty($nectar_options['blog_masonry_type'])) ? $nectar_options['blog_masonry_type'] : 'classic';


			if (
				!empty($nectar_options['blog_pagination_type']) &&
				$nectar_options['blog_pagination_type'] === 'infinite_scroll'
			) {
				$infinite_scroll_class = ' infinite_scroll';
			}

			// Store masonry style.
			if ($masonry_class !== null) {
				$masonry_style        = (!empty($nectar_options['blog_masonry_type'])) ? $nectar_options['blog_masonry_type'] : 'classic';
				$masonry_style_parsed = str_replace('_', '-', $masonry_style);
			} else {
				$standard_style_parsed = str_replace('_', '-', $blog_standard_type);
			}


			$std_minimal_class = '';
			if ($blog_standard_type == 'minimal' && $blog_type === 'std-blog-fullwidth') {
				$std_minimal_class = 'standard-minimal full-width-content';
			} elseif ($blog_standard_type == 'minimal' && $blog_type === 'std-blog-sidebar') {
				$std_minimal_class = 'standard-minimal';
			}

			if ($masonry_style === null && $blog_standard_type === 'featured_img_left') {
				$std_minimal_class = 'featured_img_left';
			}


			if ($blog_type === 'std-blog-sidebar' || $blog_type === 'masonry-blog-sidebar') {
				echo '<div class="post-area col ' . $std_minimal_class . ' span_9 ' . esc_attr($masonry_class) . ' ' . esc_attr($masonry_style) . ' ' . $infinite_scroll_class . '" data-ams="' . esc_attr($auto_masonry_spacing) . '" data-remove-post-date="' . esc_attr($remove_post_date) . '" data-remove-post-author="' . esc_attr($remove_post_author) . '" data-remove-post-comment-number="' . esc_attr($remove_post_comment_number) . '" data-remove-post-nectar-love="' . esc_attr($remove_post_nectar_love) . '"> <div class="posts-container"  data-load-animation="' . esc_attr($load_in_animation) . '">'; // WPCS: XSS ok.
			} else {

				if (
					$blog_type === 'masonry-blog-full-screen-width' && $blog_masonry_style === 'auto_meta_overlaid_spaced' ||
					$blog_type === 'masonry-blog-full-screen-width' && $blog_masonry_style === 'meta_overlaid'
				) {
					echo '<div class="full-width-content blog-fullwidth-wrap meta-overlaid">';
				} elseif ($blog_type === 'masonry-blog-full-screen-width') {
					echo '<div class="full-width-content blog-fullwidth-wrap">';
				}

				echo '<div class="post-area col ' . $std_minimal_class . ' span_12 col_last ' . esc_attr($masonry_class) . ' ' . esc_attr($masonry_style) . ' ' . $infinite_scroll_class . '" data-ams="' . esc_attr($auto_masonry_spacing) . '" data-remove-post-date="' . esc_attr($remove_post_date) . '" data-remove-post-author="' . esc_attr($remove_post_author) . '" data-remove-post-comment-number="' . esc_attr($remove_post_comment_number) . '" data-remove-post-nectar-love="' . esc_attr($remove_post_nectar_love) . '"> <div class="insights-container"  id="filter-container" data-load-animation="' . esc_attr($load_in_animation) . '">'; // WPCS: XSS ok.
			}

			add_filter('wp_get_attachment_image_attributes', 'nectar_remove_lazy_load_functionality');

			$args = array();
			// if ( isset($_REQUEST['post_type']) ) {
			// 	$args = array(
			// 		'post_type' => $_REQUEST['post_type'],
			// 		'post_status' => 'publish',
			// 	);
			// } else {
			// 	$args = array(
			// 		'post_type' => 'insight',
			// 		'post_status' => 'publish',
			// 	);
			// }

			$args = array(
				'post_type' => 'event',
				'post_status' => 'publish',
			);

			// Search by Keyword
			if ( isset($_REQUEST['search']) ) {
				$args['s'] = $_REQUEST['search'];
			}

			// create $args['meta_query'] array if one of the following fields is filled
			if (isset($_REQUEST['readtimefilter']) || isset($_REQUEST['labelfilter']))
				$args['meta_query'] = array('relation' => 'AND'); // AND means that all conditions of meta_query should be true

			//if country is set
			if( isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === "supplier"){
				if (isset($_REQUEST['countryfilter']) && $_REQUEST['countryfilter']  != "")
				$args['meta_query'][] = array(
					'key' => 'location_country',
					'value' => $_REQUEST['countryfilter'],
				);
			} else{
				if (isset($_REQUEST['countryfilter']) && $_REQUEST['countryfilter']  != "")
				$args['meta_query'][] = array(
					'key' => 'country',
					'value' => $_REQUEST['countryfilter'],
				);
			}
			if (isset($_REQUEST['categoryfilter']) && $_REQUEST['categoryfilter']  != "")
			$args['meta_query'][] = array(
				'key' => 'categories',
				'value' => $_REQUEST['categoryfilter'],
				'compare' => 'LIKE',
			);
			//$today = date("d/m/y");
			if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] === "event"){
				$args['meta_key'] = 'start_date';
				$args['orderby']  = 'meta_value';
				$args['order']    = 'ASC';
				$today = date('Y-m-d H:i:s',time());
				$args['meta_query'][] = array(
					'key' => 'start_date',
					'value' => $today,
					'compare' => '>=',
					'type' => 'DATE',
				);
		
				// Label
				if (isset($_REQUEST['labelfilter']) && $_REQUEST['labelfilter']  != "")
				$args['meta_query'][] = array(
					'key' => 'label_event',
					'value' => $_POST['labelfilter'],
				);
			} else {
		
				if (isset($_REQUEST['labelfilter']) && $_REQUEST['labelfilter']  != "")
				$args['meta_query'][] = array(
					'key' => 'label',
					'value' => $_POST['labelfilter'],
				);
			}
			
			if (!is_user_logged_in() ) {
				$args['meta_query'][] = array(
					'key' => 'is_external',
					'value' => 1,
				);
			 }

			query_posts($args);
			//echo  $GLOBALS['wp_query']->request;
			// Main post loop.
			if (have_posts()) :
				while (have_posts()) :
					the_post();
					if( get_post_status()=='private' ) continue;

					$nectar_post_format = get_post_format();

					if (
						get_post_format() === 'image' ||
						get_post_format() === 'aside' ||
						get_post_format() === 'status'
					) {
						$nectar_post_format = false;
					}

					// Masonry layouts.
					if (null !== $masonry_class) {
						//get_template_part( 'includes/partials/blog/styles/masonry-'.$masonry_style_parsed.'/entry-insight', $nectar_post_format );
					}
					// Standard layouts.
					else {
						//get_template_part( 'includes/partials/blog/styles/standard-'.$standard_style_parsed.'/entry', $nectar_post_format );
					}

					// Whatever the layout it should be entry-insight
					get_template_part('includes/partials/blog/styles/masonry-' . $masonry_style_parsed . '/entry-event', $nectar_post_format);

				endwhile;
			endif;

			?>

		</div>
		<!--/posts container-->

		<?php //nectar_pagination(); 
		?>
		<?php psn_paginator(get_pagenum_link()) ?>
		<!-- <div class="button solid_color" style="text-align: center; width:100%;">
			<a href="#" class="primary-color button regular" id="load-more">Load More</a>
		</div> -->


	</div>
	<!--/post-area-->

	<?php
	if ($blog_type === 'masonry-blog-full-screen-width') {
		echo '</div>';
	}
	?>

	<?php if ($blog_type === 'std-blog-sidebar' || $blog_type === 'masonry-blog-sidebar') { ?>
		<div id="sidebar" data-nectar-ss="<?php echo esc_attr($enable_ss); ?>" class="col span_3 col_last">
			<?php get_sidebar(); ?>
		</div>
		<!--/span_3-->
	<?php } ?>

</div>
<!--/row-->

</div>
<!--/container-->

</div>
<!--/container-wrap-->

<?php get_footer(); ?>