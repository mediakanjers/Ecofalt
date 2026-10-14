			<?php
				$mkbase_footer = apply_filters( 'mkbase_footer_variant', '' );
				if ( $mkbase_footer ) {
					get_template_part( 'template-parts/footer/layouts/footer', $mkbase_footer ); return;
				}
			?>

			<footer class="mk-footer mk-on-dark">
				<?php get_template_part('template-parts/footer/layouts/footer-default'); ?>
			</footer>
		</div> <!-- #page_container -->

		<!--  Notifications from core theme -->
		<?php get_template_part('template-parts/notifications/notifications'); ?>

		<!-- Naar boven (assets/js/naar-boven.js) -->
		<button type="button" class="mk-naar-boven" aria-label="<?php esc_attr_e('Terug naar boven', 'mk'); ?>">
			<span class="mk-naar-boven__pijl" aria-hidden="true"></span>
		</button>

		<!-- Mobile menu -->
		<?php get_template_part('template-parts/header/nav-mobile'); ?>

		<?php wp_footer(); ?>
	</body>
</html>
