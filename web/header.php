<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php // Seintje voor CSS: JS is er, scroll-animaties mogen elementen eerst verbergen (assets/js/animaties.js) ?>
	<script>document.documentElement.classList.add('js');</script>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php // Steentjes (granulaat) die met diepte in de achtergrond meebewegen ?>
<div class="mk-steentjes" aria-hidden="true">
	<div class="mk-steentjes__laag mk-steentjes__laag--groot"></div>
	<div class="mk-steentjes__laag mk-steentjes__laag--klein"></div>
</div>

<div id="page_container">

<?php
$mkbase_header = apply_filters( 'mkbase_header_variant', '' );
if ( $mkbase_header ) {
    get_template_part( 'template-parts/header/layouts/header', $mkbase_header );
} else {
    get_template_part( 'template-parts/header/layouts/header-default' );
}
?>
