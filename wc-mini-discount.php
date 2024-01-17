<?php
/*
 * Plugin Name:       WC Mini Discount
 * Plugin URI:        https://github.com/vxlrubel/wc-mini-discount
 * Description:       Introducing WC Mini Discount, your go-to solution for adding dynamic category-based discounts to your WooCommerce store! Elevate your customer shopping experience by offering targeted discounts on specific product categories, encouraging higher sales and customer loyalty.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rubel Mahmud ( Sujan )
 * Author URI:        https://www.linkedin.com/in/vxlrubel/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wc-mini-discount
 * Domain Path:       /lang
 */

//  directly access denied
 defined('ABSPATH') || exit;

 final class WC_Mini_Discount{

    private static $instance;

    public function __construct() {
        
    }

    /**
     * get instance of the class
     *
     * @return void
     */
    public static function init(){

        if ( is_null( self::$instance ) ){
            self::$instance = new self();
        }

        return self::$instance;
    }
    
 }

 function wc_mini_discount(){
    return WC_Mini_Discount::init();
 }
