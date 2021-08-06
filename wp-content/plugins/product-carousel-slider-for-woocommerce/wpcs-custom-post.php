<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Cheating, huh? Direct access to this file is not allowed !!!!' );
class WPCS_Custom_Post {
    public function __construct()
    {
        add_action( 'init', array($this, 'init') );
        add_action( 'add_meta_boxes', array($this, 'add_metabox') );
        add_action( 'edit_post', array($this, 'save_metadata') );

        // add custom column markup
        add_filter('manage_woocarousel_posts_columns',array($this, 'add_markup_custom_column_carousel_screen'));
        // output data for the custom column
        add_action('manage_woocarousel_posts_custom_column', array($this, 'manage_custom_columns_carousel_screen'), 10, 2);


    }

    /**
     * Registers WooCommerce product carousel slider post type.
     */
    function init() {
        $labels = array(
            'name'               => _x( 'WooCommerce Products Carousel Sliders', WPCS_TEXTDOMAIN ),
            'singular_name'      => _x( 'WooCommerce Products Carousel Slider', WPCS_TEXTDOMAIN ),
            'menu_name'          => _x( 'Woo Carousel', WPCS_TEXTDOMAIN ),
            'name_admin_bar'     => _x( 'Woo Carousel', WPCS_TEXTDOMAIN ),
            'add_new'            => _x( 'Add New', WPCS_TEXTDOMAIN ),
            'add_new_item'       => __( 'Add New Carousel Slider', WPCS_TEXTDOMAIN ),
            'new_item'           => __( 'New Carousel Slider', WPCS_TEXTDOMAIN ),
            'edit_item'          => __( 'Edit Carousel Slider', WPCS_TEXTDOMAIN ),
            'view_item'          => __( 'View Carousel Slider', WPCS_TEXTDOMAIN ),
            'search_items'       => __( 'Search Carousel Slider', WPCS_TEXTDOMAIN ),
            'parent_item_colon'  => __( 'Parent Carousel Sliders:', WPCS_TEXTDOMAIN ),
            'not_found'          => __( 'No carousel slider found.', WPCS_TEXTDOMAIN ),
            'not_found_in_trash' => __( 'No carousel slider found in Trash.', WPCS_TEXTDOMAIN )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ),
            'menu_icon' => 'dashicons-images-alt2'
        );

        register_post_type( 'woocarousel', $args );
    }


    /**
     * Change the columns names for our slider
     * @param array $new_columns
     *
     * @return array
     */
    function add_markup_custom_column_carousel_screen($new_columns){
        $new_columns = array();
        $new_columns['cb']   = '<input type="checkbox" />';
        $new_columns['title']   = esc_html__('Carousel Name', WPCS_TEXTDOMAIN);
        $new_columns['wpcsp_shortcode_col']   = esc_html__('Carousel Shortcode', WPCS_TEXTDOMAIN);
        //$new_columns['slider_id']   = esc_html__('Carousel ID # (helpful for widget) ', WPCS_TEXTDOMAIN); //uncomment when widgets added
        $new_columns['date']   = esc_html__('Created at', WPCS_TEXTDOMAIN);
        return $new_columns;
    }

    /**
     * @param $column_name
     * @param $post_id
     */
    function manage_custom_columns_carousel_screen($column_name, $post_id ) {

        switch($column_name){
            case 'wpcsp_shortcode_col': ?>
                <textarea style="resize: none; text-align: center; background-color: #000; color: #fff;" cols="15" rows="1" onClick="this.select();" >[wpcs id=<?php echo intval($post_id);?>]</textarea>
                <?php
                break;
            case 'slider_id':
                ?>
                <strong><?= intval($post_id); ?></strong>
                <?php
                break;

            default:
                break;

        }
    }

    /**
     * Adds a box to the main column on the WooCommerce product carousel slider post type edit screens.
     */
    function add_metabox() {
        add_meta_box(
            'wpcs_metabox',
            __( 'Settings & Shortcode Generator',WPCS_TEXTDOMAIN ),
            array($this, 'output_metabox_markup'),
            'woocarousel',
            'normal'
        );
    }


    /**
     * Prints the box content.
     * @param WP_Post $post
     */
    function output_metabox_markup( $post ) {

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'wpcs_action', 'wpcs_nonce' );
        /*@TODO; Refactor all the conditional usage of the vars in the script. Try to optimize the condition of the control structure*/
        // get the array of data from the post meta
        $enc_data = get_post_meta( $post->ID, 'wpcs', true ); // we can also extract the full var to get easy access and then before using the value of the variable, a check should be done using the empty().
        $wpcs_data_array = WooCommerce_Product_Carousel_Slider::unserialize_and_decode24($enc_data);
        $wpcs_data_array = is_array($wpcs_data_array) ? $wpcs_data_array : array();
        extract($wpcs_data_array); // lets extract the array as we know all the vars names and check all the vars before using in the input field. it helps to reduce codes without losing tight security.

        $display_full_title = !empty($display_full_title) ? $display_full_title : 'no';
        ?>
        <div id="tabs-container">

            <ul class="tabs-menu">
                <li class="current"><a href="#tab-1"> <?php esc_html_e('General Settings', WPCS_TEXTDOMAIN); ?> </a></li>
                <li><a href="#tab-2"> <?php esc_html_e('Slider Settings', WPCS_TEXTDOMAIN); ?> </a></li>
                <li><a href="#tab-3"> <?php esc_html_e('Style Settings', WPCS_TEXTDOMAIN); ?> </a></li>
            </ul>

            <div class="tab">

                <div id="tab-1" class="tab-content">
                    <div class="cmb2-wrap form-table">
                        <div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">

                            <!--Carousel  title -->
                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_title"><?php esc_html_e('Carousel Title', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-medium" name="wpcs[title]" id="wpcs_title" value="<?php if(empty($title)) { esc_html_e('Latest Product', WPCS_TEXTDOMAIN); } else { echo $title; } ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Carousel slider title', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <!--Display Full Title-->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_header"><?php esc_html_e('Display Full Title', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_full_title]" id="wpcs_display_fulltitle1" value="yes" <?php if( $display_full_title === "yes") {echo "checked"; } ?>>
                                            <label for="wpcs_display_fulltitle1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_full_title]" id="wpcs_display_fulltitle2" value="no" <?php if( !empty($display_full_title)) { checked( 'no', $display_full_title); } ?>>
                                            <label for="wpcs_display_fulltitle2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Display Full Title or not', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <!--Display Header-->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_header"><?php esc_html_e('Display Header', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_header]" id="wpcs_display_header1" value="yes" <?php if( empty($display_header) || $display_header === "yes") {echo "checked"; } ?>>
                                            <label for="wpcs_display_header1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_header]" id="wpcs_display_header2" value="no" <?php if( !empty($display_header)) { checked( 'no', $display_header); } ?>>
                                            <label for="wpcs_display_header2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Display carousel slider header or not', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <!--Repeat Product-->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_repeat_product"><?php esc_html_e('Repeat Product', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[repeat_product]" id="wpcs_repeat_product1"
                                                   value="yes" <?php if(empty($repeat_product) || 'yes' === $repeat_product) { echo 'checked'; } ?>
                                            >
                                            <label for="wpcs_repeat_product1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[repeat_product]" id="wpcs_repeat_product2"
                                                   value="no" <?php if (!empty($repeat_product)) { checked('no', $repeat_product); } ?>
                                            >
                                            <label for="wpcs_repeat_product2"><?php _e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!--Display navigation arrows-->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_header"><?php esc_html_e('Display Navigation Arrows', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_navigation_arrows]" id="wpcs_display_navigation_arrows" value="yes" <?php if( empty($display_navigation_arrows) || $display_navigation_arrows !== "no"  ) {echo "checked"; } ?>>
                                            <label for="wpcs_display_navigation_arrows"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_navigation_arrows]" id="wpcs_display_navigation_arrows2" value="no" <?php if (!empty($display_navigation_arrows)) { checked('no', $display_navigation_arrows); } ?>>
                                            <label for="wpcs_display_navigation_arrows2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                </div>
                            </div>



                            <!--Display product title-->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_product_title"><?php esc_html_e('Display Product Title', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[display_product_title]" id="wpcs_display_product_title1" value="yes" <?php if(empty($display_product_title) || 'no' !== $display_product_title) { echo 'checked'; } ?>> <label for="wpcs_display_product_title1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[display_product_title]" id="wpcs_display_product_title2" value="no" <?php if (!empty($display_product_title)) { checked('no', $display_product_title); } ?>> <label for="wpcs_display_product_title2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_price"><?php esc_html_e('Display Product Price', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[display_price]" id="wpcs_display_price1"
                                                   value="yes" <?php if(empty($display_price) || 'no' !== $display_price) { echo 'checked'; } ?>
                                            >
                                            <label for="wpcs_display_price1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[display_price]" id="wpcs_display_price2"
                                                   value="no" <?php if (!empty($display_price)) { checked('no', $display_price); } ?>
                                            >
                                            <label for="wpcs_display_price2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label></li>
                                    </ul>
                                </div>
                            </div>



                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_ratings"><?php esc_html_e('Display Product Ratings', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_ratings]" id="wpcs_display_ratings1"
                                                   value="yes" <?php if(empty($display_ratings) || 'yes' === $display_ratings) { echo 'checked'; } ?>
                                            >
                                            <label for="wpcs_display_ratings1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_ratings]" id="wpcs_display_ratings2"
                                                   value="no" <?php if (!empty($display_ratings)) { checked('no', $display_ratings); } ?>
                                            >
                                            <label for="wpcs_display_ratings2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                </div>
                            </div>



                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_display_cart"><?php esc_html_e('Display "Add to Cart" button', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_cart]" id="wpcs_display_cart1"
                                                   value="yes" <?php if(empty($display_cart) || 'yes' === $display_cart) { echo 'checked'; } ?>
                                            >
                                            <label for="wpcs_display_cart1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[display_cart]" id="wpcs_display_cart2"
                                                   value="no" <?php if (!empty($display_cart)) { checked('no', $display_cart); } ?>
                                            >
                                            <label for="wpcs_display_cart2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                </div>
                            </div>



                            <!-- Total Products -->
                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_total_products"><?php esc_html_e('Total Products to Display', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[total_products]" id="wpcs_total_products" value="<?php echo !empty($total_products) ? intval($total_products) : 12; ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('How many products to display in the carousel slider', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <!-- Products type-->
                            <div class="cmb-row cmb-type-multicheck">
                                <div class="cmb-th">
                                    <label for="wpcs_products_type"><?php esc_html_e('Products Type', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type" value="latest" <?php if( (!empty($products_type) && 'latest' == $products_type) || empty($products_type)) { echo 'checked';} ?>> <label for="wpcs_products_type"><?php esc_html_e('Latest Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type9" value="older" <?php if(!empty($products_type)) { checked('older', $products_type);}?>> <label for="wpcs_products_type9"><?php esc_html_e('Older Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type3" value="featured" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type3"><?php esc_html_e('Featured Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type1" value="onsale" <?php if(!empty($products_type)) { checked('onsale', $products_type);} ?>> <label for="wpcs_products_type1"><?php esc_html_e('On Sale Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <?php /* <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type2" value="bestselling" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type2"><?php esc_html_e('Best Selling Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type4" value="bestselling" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type4"><?php esc_html_e('Top Rated Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        */ ?>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type5" value="random" <?php if(!empty($products_type)) { checked('random', $products_type);} ?>> <label for="wpcs_products_type5"><?php esc_html_e('Random Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type6" value="category" <?php if(!empty($products_type)) { checked('category', $products_type);} ?>> <label for="wpcs_products_type6"><?php esc_html_e('Category Products', WPCS_TEXTDOMAIN); ?></label></li>
                                        <?php
                                        
                                        
                                        /*
                                        <li class="productsbyidw"><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type7" value="productsbyid" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type7"><?php esc_html_e('Products by ID', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type8" value="productsbysku" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type8"><?php esc_html_e('Products by SKU', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type9" value="productsbytag" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type9"><?php esc_html_e('Products by Tags', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type10" value="productsbyyear" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type10"><?php esc_html_e('Products by Year', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[products_type]" id="wpcs_products_type11" value="productsbymonth" <?php if(!empty($products_type)) { checked('featured', $products_type);} ?>> <label for="wpcs_products_type11"><?php esc_html_e('Products by Month', WPCS_TEXTDOMAIN); ?></label></li>
                                        */ ?>
                                    </ul>
                                    <?php
                                            $args = array('hide_empty'=> 0,
                                            'taxonomy'=> 'product_cat',
                                            'hierarchical'=>1,
                                            'name' => 'wpcs[products_cat]',
                                            'selected' => $products_cat
                                            );
                                          wp_dropdown_categories($args);
                                    ?>
                                </div>
                            </div>

                            <!--Image reszing and cropping -->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_img_crop"><?php esc_html_e('Enable Image Resizing & Cropping', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[img_crop]" id="wpcs_img_crop1" value="yes" <?php if(empty($img_crop) || 'no' !== $img_crop) { echo 'checked'; } ?>> <label for="wpcs_img_crop1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[img_crop]" id="wpcs_img_crop2" value="no" <?php if (!empty($img_crop)) { checked('no', $img_crop); } ?>> <label for="wpcs_img_crop2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label></li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('If the product images are not in the same size, this feature is helpful. It automatically resizes and crops. Note: your image must be higher than/equal to the cropping size set below. Otherwise, you may need to enable image upscaling feature from the settings below.', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>


                            <!--Enable Upscaling-->
                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_upscale"><?php esc_html_e('Enable Image Upscaling
', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[upscale]" id="wpcs_upscale1" value="yes" <?php if( empty($upscale) || $upscale === "yes" ) {echo "checked"; } ?>> <label for="wpcs_upscale1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[upscale]" id="wpcs_upscale2" value="no" <?php if( !empty($upscale)) { checked( 'no', $upscale); } ?>> <label for="wpcs_upscale2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label></li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('If the product image is less than the cropping size set above then by default, image will break. However, you can solve this problem by enabling upscaling.
', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <!--Image width-->
                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_crop_image_width"><?php esc_html_e('Image Width', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[crop_image_width]" id="wpcs_crop_image_width" value="<?php echo !empty($crop_image_width) ? intval($crop_image_width) : 300; ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Image cropping width', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <!--Image height-->
                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_crop_image_height"><?php esc_html_e('Image Height', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[crop_image_height]" id="wpcs_crop_image_height"
                                           value="<?php echo !empty($crop_image_height) ? intval($crop_image_height) : 300; ?>"
                                    >
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Image cropping height', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div id="tab-2" class="tab-content">

                    <div class="cmb2-wrap form-table">
                        <div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">

                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_auto_play"><?php esc_html_e('Auto Play', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[auto_play]" id="wpcs_auto_play1"
                                                   value="true" <?php if(empty($auto_play) || 'true' === $auto_play) { echo 'checked'; } ?>
                                            >

                                            <label for="wpcs_auto_play1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                        <li>
                                            <input type="radio" class="cmb2-option" name="wpcs[auto_play]" id="wpcs_auto_play2"
                                                   value="false" <?php if (!empty($auto_play)) { checked('false', $auto_play); } ?>
                                            >
                                            <label for="wpcs_auto_play2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label>
                                        </li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Slider would automatically play or not', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>



                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_stop_on_hover"><?php esc_html_e('Stop on Hover', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[stop_on_hover]" id="wpcs_stop_on_hover1" value="true" <?php if(empty($stop_on_hover) || 'true' === $stop_on_hover) { echo 'checked'; } ?>> <label for="wpcs_stop_on_hover1"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[stop_on_hover]" id="wpcs_stop_on_hover2" value="false" <?php if (!empty($stop_on_hover)) { checked('false', $stop_on_hover); } ?>> <label for="wpcs_stop_on_hover2"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label></li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Stop autoplay on mouse hover or not', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_slide_speed"><?php esc_html_e('Slide Speed', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[slide_speed]" id="wpcs_slide_speed" value="<?php if(!empty($slide_speed)) { echo $slide_speed; } else { echo 4000; } ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('1 second = 1000', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_items"><?php esc_html_e('Products to display on Desktop', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[items]" id="wpcs_items" value="<?php if(!empty($items)) { echo $items; } else { echo 4; } ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Maximum number of products to display at a time with the widest browser width. eg on screen size > 1198px', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcsp_laptop_items"><?php esc_html_e('Products to display on Laptop', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[laptop_items]" id="wpcsp_laptop_items" value="<?php echo !empty($laptop_items)? intval($laptop_items): 4;  ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Maximum number of products to display at a time on a laptop screen. Eg. Screen size > 978 px.', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcsp_tablet_items"><?php esc_html_e('Products to Display on Tablet', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[tablet_items]" id="wpcsp_tablet_items" value="<?php echo !empty($tablet_items)? intval($tablet_items): 2; ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Maximum number of products to display at a time on Tablet devices. Eg. screen size > 768px', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcsp_mobile_items"><?php esc_html_e('Products to Display on Mobile', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[mobile_items]" id="wpcsp_mobile_items" value="<?php echo !empty($mobile_items)? intval($mobile_items): 1; ?>">
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Maximum number of products to display at a time on Mobile. eg. screen size from 0px - 480px', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-radio">
                                <div class="cmb-th">
                                    <label for="wpcs_pagination"><?php esc_html_e('Pagination', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <ul class="cmb2-radio-list cmb2-list">
                                        <li><input type="radio" class="cmb2-option" name="wpcs[pagination]" id="wpcs_pagination1" value="false" <?php if(empty($pagination) || 'false' === $pagination) { echo 'checked'; } ?>> <label for="wpcs_pagination1"><?php esc_html_e('No', WPCS_TEXTDOMAIN); ?></label></li>
                                        <li><input type="radio" class="cmb2-option" name="wpcs[pagination]" id="wpcs_pagination2" value="true" <?php if (!empty($pagination)) { checked('true', $pagination); } ?>> <label for="wpcs_pagination2"><?php esc_html_e('Yes', WPCS_TEXTDOMAIN); ?></label></li>
                                    </ul>
                                    <p class="cmb2-metabox-description"><?php esc_html_e('Show pagination or not', WPCS_TEXTDOMAIN); ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>




                <div id="tab-3" class="tab-content">
                    <div class="cmb2-wrap form-table">
                        <div id="cmb2-metabox" class="cmb2-metabox cmb-field-list">

                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_header_title_font_size"><?php esc_html_e('Carousel Title Font Size', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[header_title_font_size]" id="wpcs_header_title_font_size" value="<?php if(!empty($header_title_font_size)) { echo $header_title_font_size; } ?>" placeholder="e.g. 20px">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_header_title_font_color"><?php esc_html_e('Carousel Title Font Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[header_title_font_color]" id="wpcs_header_title_font_color" value="<?php if(!empty($header_title_font_color)) { echo $header_title_font_color; } else { echo "#303030"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_nav_arrow_color"><?php esc_html_e('Navigational Arrow Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[nav_arrow_color]" id="wpcs_nav_arrow_color" value="<?php if(!empty($nav_arrow_color)) { echo $nav_arrow_color; } else { echo "#FFFFFF"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_nav_arrow_bg_color"><?php esc_html_e('Navigational Arrow Background Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[nav_arrow_bg_color]" id="wpcs_nav_arrow_bg_color" value="<?php if(!empty($nav_arrow_bg_color)) { echo $nav_arrow_bg_color; } else { echo "#BBBBBB"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_nav_arrow_hover_color"><?php esc_html_e('Navigational Arrow Hover Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[nav_arrow_hover_color]" id="wpcs_nav_arrow_hover_color" value="<?php if(!empty($nav_arrow_hover_color)) { echo $nav_arrow_hover_color; } else { echo "#FFFFFF"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_nav_arrow_bg_hover_color"><?php esc_html_e('Navigational Arrow Background Hover Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[nav_arrow_bg_hover_color]" id="wpcs_nav_arrow_bg_hover_color" value="<?php if(!empty($nav_arrow_bg_hover_color)) { echo $nav_arrow_bg_hover_color; } else { echo "#9A9A9A"; } ?>">
                                </div>
                            </div>





                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_title_font_size"><?php esc_html_e('Product Title Font Size', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[title_font_size]" id="wpcs_title_font_size" value="<?php if(!empty($title_font_size)) { echo $title_font_size; } else { echo "16px"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_title_font_color"><?php esc_html_e('Product Title Font Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[title_font_color]" id="wpcs_title_font_color" value="<?php if(!empty($title_font_color)) { echo $title_font_color; } else { echo "#444444"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_title_hover_font_color"><?php esc_html_e('Product Title Hover Font Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[title_hover_font_color]" id="wpcs_title_hover_font_color" value="<?php if(!empty($title_hover_font_color)) { echo $title_hover_font_color; } else { echo "#000"; } ?>">
                                </div>
                            </div>





                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_price_font_size"><?php esc_html_e('Product Price Font Size', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[price_font_size]" id="wpcs_price_font_size" value="<?php if(!empty($price_font_size)) { echo $price_font_size; } else { echo "18px"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_price_font_color"><?php esc_html_e('Product Price Font Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[price_font_color]" id="wpcs_price_font_color" value="<?php if(!empty($price_font_color)) { echo $price_font_color; } else { echo "#444444"; } ?>">
                                </div>
                            </div>





                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_ratings_color"><?php esc_html_e('Product Ratings Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[ratings_color]" id="wpcs_ratings_color"
                                           value="<?php if(!empty($ratings_color)) { echo $ratings_color; } else { echo "#FFC500"; } ?>"
                                    >
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_ratings_size"><?php esc_html_e('Product Ratings Size', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[ratings_size]" id="wpcs_ratings_size" value="<?php if(!empty($ratings_size)) { echo $ratings_size; } else { echo "1em"; } ?>">
                                </div>
                            </div>





                            <div class="cmb-row cmb-type-text-medium">
                                <div class="cmb-th">
                                    <label for="wpcs_cart_font_size"><?php esc_html_e('"Add to Cart" Button Font Size', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small" name="wpcs[cart_font_size]" id="wpcs_cart_font_size" value="<?php if(!empty($cart_font_size)) { echo $cart_font_size; } else { echo "14px"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_cart_font_color"><?php esc_html_e('"Add to Cart" Font Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[cart_font_color]" id="wpcs_cart_font_color" value="<?php if(!empty($cart_font_color)) { echo $cart_font_color; } else { echo "#ffffff"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_cart_bg_color"><?php esc_html_e('"Add to Cart" Button Background Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[cart_bg_color]" id="wpcs_cart_bg_color" value="<?php if(!empty($cart_bg_color)) { echo $cart_bg_color; } else { echo "#BBBBBB"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_cart_button_hover_color"><?php esc_html_e('"Add to Cart" Button Hover Background Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[cart_button_hover_color]" id="wpcs_cart_button_hover_color" value="<?php if(!empty($cart_button_hover_color)) { echo $cart_button_hover_color; } else { echo "#9A9A9A"; } ?>">
                                </div>
                            </div>


                            <div class="cmb-row cmb-type-colorpicker">
                                <div class="cmb-th">
                                    <label for="wpcs_cart_button_hover_font_color"><?php esc_html_e('"Add to Cart" Hover Font Color', WPCS_TEXTDOMAIN); ?></label>
                                </div>
                                <div class="cmb-td">
                                    <input type="text" class="cmb2-text-small wpcs-color-picker" name="wpcs[cart_button_hover_font_color]" id="wpcs_cart_button_hover_font_color" value="<?php if( !empty($cart_button_hover_font_color) ) { echo $cart_button_hover_font_color; } else { echo "#ffffff"; } ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            </div> <!-- end tab -->
        </div> <!-- end tabs-container -->

        <div class="wpcs_shortcode">
            <h2><?php esc_html_e('Shortcode', WPCS_TEXTDOMAIN); ?> </h2>
            <p><?php esc_html_e('Use following shortcode to display the Carousel Slider anywhere:', WPCS_TEXTDOMAIN); ?></p>
            <textarea cols="25" rows="1" onClick="this.select();" >[wpcs <?php echo 'id="'.$post->ID.'"';?>]</textarea> <br />

            <p><?php esc_html_e('If you need to put the shortcode in code/theme file, use this:', WPCS_TEXTDOMAIN); ?></p>
            <textarea cols="54" rows="1" onClick="this.select();" ><?php echo '<?php echo do_shortcode("[wpcs id='.$post->ID.']"); ?>'; ?></textarea>
        </div>
    <?php }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    function save_metadata( $post_id ) {
        /*
             * We need to verify this came from our screen and with proper authorization,
             * because the save_post action can be triggered at other times.
             */

        if (! $this->_wpcs_security_check($post_id)) return; // vail if the security does not pass the test.

        $wpcs_encoded = !empty($_POST['wpcs']) ? WooCommerce_Product_Carousel_Slider::serialize_and_encode24($_POST['wpcs']) : WooCommerce_Product_Carousel_Slider::serialize_and_encode24(array());
        update_post_meta($post_id, "wpcs", $wpcs_encoded);



    }

    /**
     * It checks if the nonce is valid and if a user is allowed to save the data or if it is an autosave action
     * @param int $post_id              The id of the current post
     * @param string $nonce_name        [Optional] Name of the nonce variable in the $_POST var
     * @param string $action            [Optional] Name of the nonce action variable in the $_POST var
     * @access private
     * @return bool                     It returns true if the checks Passes. Otherwise false is returned.
     */
    private function _wpcs_security_check($post_id, $nonce_name = 'wpcs_nonce', $action = 'wpcs_action' ){
        // checks are divided into 4 parts for readability.
        if (  empty($_REQUEST[$nonce_name]) || (!empty( $_REQUEST[$nonce_name] ) && !wp_verify_nonce( $_REQUEST[$nonce_name], $action )) ) {
            return false;
        }
        // are we working with the data of our post ?
        if( empty($_REQUEST['post_type']) || (!empty($_REQUEST['post_type']) && 'woocarousel' !== $_REQUEST['post_type']) ){
            return false;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything. returns false
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }
        // Check the user's permissions.
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return false;
        }

        return true;
    }
}