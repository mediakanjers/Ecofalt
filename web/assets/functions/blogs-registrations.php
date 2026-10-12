<?php
    add_action('acf/init', function() {

        // VOORBEELD BLOCK REGISTRATIE
        // if( function_exists('acf_register_block_type') ) {
        //     /* === BLOCK NAME === */
        //     acf_register_block_type([
        //         'name'            => 'BLOCK NAME',
        //         'title'           => __('BLOCK NAME'),
        //         'description'     => __('BLOCK NAME'),
        //         'category'        => 'layout',
        //         'icon'            => 'cover-image',
        //         'mode'            => 'edit',
        //         'render_callback' => function( $block, $content = '', $is_preview = false, $post_id = 0 ) {
        //             $template = get_stylesheet_directory() . '/template-parts/blocks/BLOCK NAME/BLOCK NAME.php';
        //             if( file_exists($template) ) {
        //                 include $template;
        //             }
        //         }
        //     ]);
        // }

    });
?>