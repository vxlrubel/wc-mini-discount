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

 use WCMD\Admin\Admin_Menu;

 //  include autoload file
 if ( file_exists( dirname(__FILE__) . '/inc/autoload.php' ) ){
    require_once dirname(__FILE__) . '/inc/autoload.php';
 }
 
 final class WC_Mini_Discount{

    private static $instance;

    public function __construct() {
        // woocommerce activate notice
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            add_action( 'admin_notices', [ $this, 'missing_wc_notice' ] );
        }

        // register text-domain
        add_action( 'plugins_loaded', [ $this, 'register_text_domain' ] );

        // render discount price
        add_filter( 'woocommerce_get_price_html', [ $this, 'display_discounted_price' ], 10, 2 );

        // create admin menu
        new Admin_Menu;

        register_activation_hook( __FILE__, [ $this, 'create_db_table' ] );

        // admin scripts
        add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
    }

    /**
     * admin script
     *
     * @return void
     */
    public function register_scripts(){
        if ( $_GET['page'] && $_GET['page'] == 'wc-mini-discount' ){
            wp_enqueue_style( 
              'wc-admin-style',
              trailingslashit( plugins_url( 'wc-style.css', __FILE__ ) )
            );
        }
    }

    /**
     * create database table
     *
     * @return void
     */
    public function create_db_table(){
        global $wpdb;
        $table           = $wpdb->prefix . 'wc_mini_discount';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table(
            ID mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (ID)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( $sql );
    }

    /**
     * set discount
     *
     * @return void
     */
    private function get_discount_percentage(){
        $get_discount = ! is_null( get_option( '_wc_mini_discount' ) ) ? sanitize_text_field( get_option( '_wc_mini_discount' ) ) : '';
        return $get_discount;
    }



    /**
     * set discounted categories
     *
     * @return void
     */
    private function get_category(){
        global $wpdb;
        $table = $wpdb->prefix . 'wc_mini_discount';
        $sql = "SELECT name FROM $table";
        $results = $wpdb->get_results( $sql, ARRAY_A );

        $categories = [];

        foreach ( $results as $result ) {
            $categories[] = $result['name'];
        }
        
        return $categories;
    }
    /**
     * get apply discount
     *
     * @param [type] $price
     * @param [type] $product
     * @return $price
     */
    private function get_apply_discount( $price, $product ){
        if ( is_user_logged_in() ){
            $get_discount = $this->get_discount_percentage();
            $categories   = $this->get_category();

            if( has_term( $categories, 'product_cat', $product->get_id() ) ){
                $discount  = ( $get_discount / 100 ) * $price;
                $price    -= $discount;
            }
        }

        return $price;
    }

    /**
     * display discount price
     *
     * @param [type] $price
     * @param [type] $product
     * @return $price
     */
    public function display_discounted_price( $price, $product ){
        
        $discounted_price = $this->get_apply_discount( $product->get_price(), $product );

        if ( $discounted_price !== $product->get_price() ){
            $price = '<del>' . wc_price($product->get_price()) . '</del> <ins>' . wc_price($discounted_price) . '</ins>';
        }
        
        return $price;
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
