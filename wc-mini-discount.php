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
        // woocommerce activate notice
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action( 'admin_notices', [ $this, 'missing_wc_notice' ] );
        }

        add_action( 'plugins_loaded', [ $this, 'register_text_domain' ] );
    }

    /**
     * register text domain
     *
     * @return void
     */
    public function register_text_domain(){
        load_plugin_textdomain( 
            'wc-mini-discount',
            false,
            dirname( plugin_basename( __FILE__ ) ) . trailingslashit( '/lang' )
        );
    }


    /**
     * display notice if Woocommerce is not active
     *
     * @return void
     */
    public function missing_wc_notice(){
        ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e( 'WC Mini Discount requires WooCommerce to be installed and activated.', 'wc-mini-discount' ); ?></p>
            </div>
        <?php
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
 wc_mini_discount();
