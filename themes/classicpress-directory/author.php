<?php
/**
 * The template for displaying author archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
$author_id = get_queried_object_id();
$author = get_user_by( 'id', $author_id );

get_header();
?>

	<div id="primary">
		<main id="main">

			<header>
				<h1><?php echo $author->display_name; ?></h1>
			</header>

			<?php
			if ( class_exists( 'SearchAndFilter' ) ) {
				echo do_shortcode( '[searchandfilter fields="search,post_types,category,post_tag" post_types="plugin,theme,snippet"]' );
			}
			?>

			<div class="clear"></div>

			<div id="tabs" class="ui-tabs">
				<div id="ui-tabs-nav" class="ui-tabs-nav" role="tablist">

					<button id="ui-id-1" class="ui-button ui-state-active plugins" aria-controls="tabs-1" aria-selected="true" role="tab" tabindex="0">Plugins</button>

					<button id="ui-id-2" class="ui-button" aria-controls="tabs-2" aria-selected="false" role="tab" tabindex="-1">Themes</button>

					<button id="ui-id-3" class="ui-button" aria-controls="tabs-3" aria-selected="false" role="tab" tabindex="-1">Snippets</button>

				</div><!-- #ui-tabs-nav -->

				<div id="tabs-1" class="ui-panel" role="tabpanel">
					<ul class="software-grid">

					<?php
					$plugin_args = array(
						'post_type'		=> 'plugin',
						'post_status'	=> 'publish',
						'author'		=> $author_id,
					);
					$plugin_post_loop = new WP_Query( $plugin_args );

					if ( $plugin_post_loop->have_posts() ) :

						/* Start the Loop */
						while ( $plugin_post_loop->have_posts() ) :
						?>
			
							<li>

								<?php
								$plugin_post_loop->the_post();

								get_template_part( 'template-parts/content', get_post_type() );
								?>
							</li>

						<?php
						endwhile;

					endif;
					wp_reset_postdata();
					?>
				
					</ul>

				</div><!-- #tabs-1 -->

				<div id="tabs-2" class="ui-panel" role="tabpanel" hidden>
					<ul class="software-grid">

					<?php
					$theme_args = array(
						'post_type'		=> 'theme',
						'post_status'	=> 'publish',
						'author'		=> $author_id,
					);
					$theme_post_loop = new WP_Query( $theme_args );

					if ( $theme_post_loop->have_posts() ) :

						/* Start the Loop */
						while ( $theme_post_loop->have_posts() ) :
						?>
			
							<li>
								<?php
								$theme_post_loop->the_post();

								get_template_part( 'template-parts/content', get_post_type() );
								?>
							</li>

						<?php
						endwhile;

					endif;
					wp_reset_postdata();
					?>
				
					</ul>

				</div><!-- #tabs-2 -->

				<div id="tabs-3" class="ui-panel" role="tabpanel" hidden>
					<ul class="software-grid">

					<?php
					$snippet_args = array(
						'post_type'		=> 'snippet',
						'post_status'	=> 'publish',
						'author'		=> $author_id,
					);
					$snippet_post_loop = new WP_Query( $snippet_args );

					if ( $snippet_post_loop->have_posts() ) :

						/* Start the Loop */
						while ( $snippet_post_loop->have_posts() ) :
						?>
			
							<li>
								<?php
								$snippet_post_loop->the_post();

								get_template_part( 'template-parts/content', get_post_type() );
								?>
							</li>

						<?php
						endwhile;

					endif;
					wp_reset_postdata();
					?>
				
					</ul>

				</div><!-- #tabs-3 -->

			</div>

			<?php
			the_posts_navigation();
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
