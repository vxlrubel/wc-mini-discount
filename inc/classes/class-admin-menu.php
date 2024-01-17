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
            'manage_options',                          // capability
            'wc-mini-discount',                        // menu slug
            [ $this, 'render_page_content' ],          // callback function
            'dashicons-cart',                          // icon
            25                                         //position
        );
    }

    /**
     * set discount
     *
     * @return void
     */
    private function set_discount_price(){
        if ( isset( $_REQUEST['set_discount'] ) && ! empty( $_REQUEST['set_discount'] ) ){
            update_option( '_wc_mini_discount', $_REQUEST['set_discount'] );
        }
    }

    /**
     * add category
     *
     * @return void
     */
    public function add_category(){
        global $wpdb;
        $table = $wpdb->prefix . 'wc_mini_discount';

        if ( isset( $_REQUEST['add_category'] ) && ! empty( $_REQUEST['add_category'] ) ){
            $category_name = sanitize_text_field( $_REQUEST['add_category'] );

            $data = [
                'name' => $category_name
            ];

            $wpdb->insert( $table, $data );
        }
    }

    /**
     * get results
     *
     * @return void
     */
    private function get_results(){
        global $wpdb;
        $table = $wpdb->prefix . 'wc_mini_discount';
        $sql = "SELECT name FROM $table";
        $results = $wpdb->get_results( $sql, ARRAY_A );

        $items = [];

        foreach ( $results as $result ) {
            $items[] = $result['name'];
        }
        
        return $items;
    }

    /**
     * render the page content
     *
     * @return void
     */
    public function render_page_content(){
        $action = $_SERVER['PHP_SELF'] . '?page=wc-mini-discount';
        $this->set_discount_price();
        $this->add_category();
        $items = $this->get_results();
        ?>
        <div class="wrap wc-mini-discount">
            <h2>WC Mini Discount</h2>

            <div class="wrapper-box">
                <div class="left-side">

                    <!-- set discount -->
                    <form action="<?php echo esc_url( $action ); ?>" method="POST">
                        <input type="number" name="set_discount" min="0" max="100">
                        <p>
                            <input type="submit" value="Set Discount Price" class="button button-primary">
                        </p>
                    </form>

                    <!-- add category -->
                    <form action="<?php echo esc_url( $action ); ?>" method="POST">
                        <input type="text" name="add_category" min="0" max="100">
                        <p>
                            <input type="submit" value="Add New Category" class="button button-primary">
                        </p>
                    </form>

                </div>
                <div class="right-side">
                    <ul>
                        <?php
                            foreach ( $items as $item ) {
                                printf( '<li>%s</li>', $item );
                            }
                        ?>
                        <li> category items</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <?php
    }
}