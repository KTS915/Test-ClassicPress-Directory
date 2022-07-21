<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_header();
?>

	<div id="primary">
		<main id="main">

		<?php
		if ( have_posts() ) :
		?>

			<header>
				<?php
				the_archive_title( '<h1>', '</h1>' );
				the_archive_description( '<div>', '</div>' );
				?>
			</header>

			<?php
			if ( class_exists( 'SearchAndFilter' ) ) {
				echo do_shortcode( '[searchandfilter fields="search,post_types,category,post_tag" post_types="plugin,theme,snippet"]' );
			}
			?>

			<div class="clear"></div>

			<ul class="software-grid">

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
			?>
			
				<li>
					<?php
					the_post();

					get_template_part( 'template-parts/content', get_post_type() );
					?>
				</li>

			<?php
			endwhile;
			?>
				
			</ul>

			<?php
			the_posts_pagination( array(
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'classicpress' ) . ' </span>',
			) );

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
