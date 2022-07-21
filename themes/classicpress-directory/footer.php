<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>
		</div><!-- #content -->

		<?php get_sidebar( 'sidebar2' ); ?>

	</div><!-- #outer-content -->
	
	<footer id="colophon">
		<div class="classic">
			<div class="footerleft">
				<a href="/"><img src="https://www.classicpress.net/wp-content/themes/classicpress-susty-child/images/icon-white.svg" alt="ClassicPress"></a>
				<p class="registration">The ClassicPress project is under the direction of The ClassicPress Initiative, a nonprofit organization registered under section 501(c)(3) of the United States IRS code.</p>
				<ul class="nav">
					<li><a href="https://www.classicpress.net/contact/">Contact Us</a></li>
				</ul>
			</div>
			<div class="footerright">
				<div class="menu-footermenu-container">
					<ul id="footmenu" class="nav">
						<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="https://www.classicpress.net/contact/">Press Inquiries</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://forums.classicpress.net/c/support">Get Support</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://www.classicpress.net/join-slack/">Chat with us on Slack</a></li>
						<li class="menu-item menu-item-type-custom menu-item-object-custom"><a target="_blank" rel="noreferrer noopener" href="https://forums.classicpress.net/c/governance/petitions/77">Start or Vote on a Petition</a></li>
					</ul>
				</div>
			</div>
		</div>
	</footer>

	<footer id="legal">
		<div class="cplegal">
			<div class="cpcopyright">
				<p>© 2018-<?php echo date( 'Y' ); ?> ClassicPress. All Rights Reserved.</p>
			</div>
			<div class="cppolicy">
				<p><a href="https://www.classicpress.net/privacy-policy/">Privacy Policy</a></p>
			</div>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
