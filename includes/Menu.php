<?php
namespace AVCL\Blocks;
class Menu extends Init{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'menu']);
        
    }

    public function menu(){
        // add_menu_page(
        //     __('AVCL Blocks', 'avcl-blocks'),
        //     __('AVCL Blocks', 'avcl-blocks'),
        //     'manage_options',
        //     'avcl-blocks',
        //     '__return_null',
        //     'dashicons-screenoptions'
        // );

        // add_submenu_page(
        //     'avcl-blocks',
        //     __('Settings', 'avcl-blocks'),
        //     __('Settings', 'avcl-blocks'),
        //     'manage_options',
        //     'avcl-blocks-settings',
        //     function () {
        //         echo '<div class="wrap" id="avcl-blocks-settings"></div>';
        //     }
        // );
    }
    
}