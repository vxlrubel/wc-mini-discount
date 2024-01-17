<?php
namespace WCMD\Admin;

defined('ABSPATH') || exit;

class Admin_Menu{

    public function __construct(){
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * create admin menu
     *
     * @return void
     */
    public function admin_menu(){
        add_menu_page(
            __( 'Mini Discount', 'wc-mini-discount' ), // page title
            __( 'Mini Discount', 'wc-mini-discount' ), // menu title
            'manage_options',                             // capability
            'wc-mini-discount',                        // menu slug
            [ $this, 'render_page_content' ],           // callback function
            'dashicons-editor-ul',                        // icon
            25                                            //position
        );
    }
}