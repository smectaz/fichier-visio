<?php

global $post;
$limit        = isset( $instance['limit'] ) ? $instance['limit'] : 4;
$item_visible = isset( $instance['slider-options']['item_visible'] ) ? $instance['slider-options']['item_visible'] : 3;
$pagination   = isset( $instance['slider-options']['show_pagination'] ) ? $instance['slider-options']['show_pagination'] : 0;
$navigation   = isset( $instance['slider-options']['show_navigation'] ) ? $instance['slider-options']['show_navigation'] : 0;
$autoplay     = isset( $instance['slider-options']['auto_play'] ) ? $instance['slider-options']['auto_play'] : 0;
$featured     = ! empty( $instance['featured'] ) ? true : false;
$thumb_w      = ( ! empty( $instance['thumbnail_width'] ) && '' != $instance['thumbnail_width'] ) ? $instance['thumbnail_width'] : apply_filters( 'thim_course_thumbnail_width', 450 );
$thumb_h      = ( ! empty( $instance['thumbnail_height'] ) && '' != $instance['thumbnail_height'] ) ? $instance['thumbnail_height'] : apply_filters( 'thim_course_thumbnail_height', 400 );

$condition = array(
	'post_type'           => 'lp_course',
	'posts_per_page'      => $limit,
	'ignore_sticky_posts' => true,
);
$sort      = $instance['order'];

if ( $sort == 'category' && $instance['cat_id'] && $instance['cat_id'] != 'all' ) {
	if ( get_term( $instance['cat_id'], 'course_category' ) ) {
		$condition['tax_query'] = array(
			array(
				'taxonomy' => 'course_category',
				'field'    => 'term_id',
				'terms'    => $instance['cat_id']
			),
		);
	}
}


if ( $sort == 'popular' ) {
	$post_in = eduma_lp_get_popular_courses( $limit );

	$condition['post__in'] = $post_in;
	$condition['orderby']  = 'post__in';
}

if ( $featured ) {
	$condition['meta_query'] = array(
		array(
			'key'   => '_lp_featured',
			'value' => 'yes',
		)
	);
}

$the_query = new WP_Query( $condition );

if ( $the_query->have_posts() ) :
	if ( $instance['title'] ) {
		echo ent2ncr( $args['before_title'] . $instance['title'] . $args['after_title'] );
	}

	?>
	<div class="thim-carousel-wrapper thim-course-carousel thim-course-grid"
		 data-visible="<?php echo esc_attr( $item_visible ); ?>"
		 data-pagination="<?php echo esc_attr( $pagination ); ?>"
		 data-navigation="<?php echo esc_attr( $navigation ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
			<div class="course-item">

				<div class="course-thumbnail">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>">
						<?php echo thim_get_feature_image( get_post_thumbnail_id( get_the_ID() ), 'full', $thumb_w, $thumb_h, get_the_title() ); ?>
					</a>
					<?php
					do_action( 'thim_inner_thumbnail_course' );

					// only button read more
					do_action( 'thim-lp-course-button-read-more' );
					?>

				</div>

				<div class="thim-course-content">
					<?php
					learn_press_courses_loop_item_instructor();

					the_title( sprintf( '<h2 class="course-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
					do_action( 'learn_press_after_the_title' );
					?>
					
										<p>
						parent
					</p>
					<?php if ( class_exists( 'LP_Addon_Coming_Soon_Courses_Preload' ) && learn_press_is_coming_soon( get_the_ID() ) ): ?>
						<div class="message message-warning learn-press-message coming-soon-message">
							<?php esc_html_e( 'Coming soon', 'eduma' ) ?>
						</div>
					<?php else:  

						do_action( 'learnpress_loop_item_course_meta' );
					
					endif; ?>
				</div>

			</div>
		<?php
		endwhile;
		?>
	</div>

<?php
endif;
wp_reset_postdata();