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
     * get results
     *
     * @return void
     */
    private function get_results(){
        global $wpdb;
        $table = $wpdb->prefix . 'wc_mini_discount';
        $sql = "SELECT ID, category_slug, discount_price FROM $table ORDER BY ID DESC";
        $results = $wpdb->get_results( $sql, ARRAY_A );

        $items = [];

        foreach ( $results as $result ) {
            $items[] = $result;
        }
        
        return $items;
    }

    /**
     * insert category with discount price
     *
     * @return void
     */
    public function insert_category_discount(){
        global $wpdb;
        $table = $wpdb->prefix . 'wc_mini_discount';

        if ( isset( $_REQUEST['category_slug'] ) && ! empty( $_REQUEST['category_slug'] ) && isset( $_REQUEST['discount_price'] ) &&  ! empty( $_REQUEST['discount_price'] ) ) {
            $category_slug     = sanitize_text_field( $_REQUEST['category_slug'] );
            $discount_price    = trim( $_REQUEST['discount_price'] );
            $existing_category = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE category_slug = %s", $category_slug));

            $data = [
                'category_slug'  => $category_slug,
                'discount_price' => $discount_price,
            ];

            if ( ! $existing_category ){

                $result = $wpdb->insert( $table, $data );

                if( $result === false ){
                    echo 'something went wrong.';
                }
                printf(
                    '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                    esc_html( 'Add new record successfull.' )
                );

            }else{
                printf(
                    '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                    esc_html( 'Category already exists.' )
                );
            }

        }else{
            if ( isset( $_REQUEST['discount_price'] ) && empty( $_REQUEST['discount_price'] ) ){
                printf(
                    '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
                    esc_html( 'Discount price is not set.' )
                );
            }
        }
    }

    /**
     * render the page content
     *
     * @return void
     */
    public function render_page_content(){
        $action = $_SERVER['PHP_SELF'] . '?page=wc-mini-discount';
        $this->set_discount_price();
        $this->insert_category_discount();
        $items = $this->get_results();
        $link  = esc_url( admin_url( 'admin.php?page=wc-mini-discount' ) );

        $get_discount_count = ! empty( get_option( '_wc_mini_discount' ) ) ? sanitize_text_field( get_option( '_wc_mini_discount' ) ) : 0;
        
        ?>
        <div class="wrap wc-mini-discount">
            <h2>WC Mini Discount</h2>

            <div class="wrapper-box">
                <div class="left-side">
                    <h3 class="inner-title">Add New Discount Offers!</h3>

                    <form action="<?php echo $action; ?>" method="POST">
                        <div class="form-box">
                            <div class="category">
                                <?php
                                    $get_categories = get_terms(
                                        [
                                            'taxonomy'   => 'product_cat',
                                            'hide_empty' => false,
                                        ]
                                    );
                                    echo '<label for="add-category">Add Category Name: </label>';
                                    echo '<select class="widefat" name="category_slug" id="add-category">';

                                    foreach ( $get_categories as $category ) {
                                        printf(
                                            '<option value="%1$s">%2$s</option>',
                                            esc_attr( $category->name ),
                                            esc_html__( $category->name, 'wc-mini-discount' ),
                                        );
                                    }
                                    echo '</select>';
                                ?>
                            </div>
                            <div class="discount-price">
                                <label for="set-price">Set Price: </label>
                                <input type="number" id="set-price" name="discount_price" min="0" max="100" class="widefat">
                            </div>
                        </div>
                        <p>
                            <input type="submit" value="Add New Discount" class="button button-primary">
                        </p>
                    </form>
                </div>

                <div class="right-side">
                    <h3 class="inner-title">Category List</h3>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Discount Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php 

                                if ( count( $items ) > 0 ): ?>
                                    <?php 
                                        foreach ( $items as $item ) : 
                                            $edit_link   = $link . '&' . 'discount_edit=' . $item['ID'];
                                            $delete_link = $link . '&' . 'discount_delete=' . $item['ID'];
                                        ?>

                                            <tr>
                                                <td style="text-transform: capitalize;"><?php echo $item['category_slug'];?></td>
                                                <td><?php echo $item['discount_price'];?> %</td>
                                                <td>
                                                    <a href="<?php echo esc_url( $edit_link ); ?>">Edit</a>
                                                    <a href="<?php echo esc_url( $delete_link ); ?>">Delete</a>
                                                </td>
                                            </tr>
            
                                        <?php endforeach;
                                    ?>
                                <?php else: ?>
                                    <tr><td colspan="3">No Result Found.</td></tr>
                                <?php endif;
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Category Name</th>
                                <th>Discount Price</th>
                                <th>Actions</th>
                            </tr>
                        </tfoor>
                    </table>
                </div>
            </div>
        </div>
        
        <?php
    }
}