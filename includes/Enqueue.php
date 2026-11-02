<?php

namespace AVCL\Blocks;


class Enqueue extends Init{
    
    public function __construct() {
     add_action('admin_enqueue_scripts', [$this, 'enqueue']);   
    }

    public function enqueue(string $hook){
        if ($hook !== 'avcl-blocks_page_avcl-blocks-settings') {
            return;
        }

        $plugin_dir = dirname( __DIR__ );
        $asset_file = $plugin_dir . '/build/admin/index.asset.php';

        if (!file_exists($asset_file)) {
            return;
        }
        $asset = require $asset_file;

         wp_enqueue_script(
            'avcl-blocks-settings',
            plugins_url( 'build/admin/index.js', $plugin_dir . '/avcl-blocks.php' ),
            $asset['dependencies'],
            $asset['version'],
            true
        );

         $style_file = $plugin_dir . '/build/admin/style-index.css';
         if ( file_exists( $style_file ) ) {
             wp_enqueue_style(
                 'avcl-blocks-settings',
                 plugins_url( 'build/admin/style-index.css', $plugin_dir . '/avcl-blocks.php' ),
                 [ 'wp-components' ],
                 filemtime( $style_file )
             );
         }

         wp_enqueue_style('wp-components');

    }

    

    
}