<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Woo_Bday{

    /**
     * Testing Suite
     */
    private $testing;
    private $testing_suite;

    /**
     * Custom roles
     */
    private $roles;

    /**
     * Admin
     */
    private $admin;

    /**
     * The Constructor.
     */
    function __construct() {

        /**
         * No ajax here.
         */
        if( defined( 'DOING_AJAX') ) 
            return;

        /**
         * Whether testing functions enabled.
         * 
         * When enabled user creation on activation, etc. will execute.
         */
        $this->testing = true;


        /**
         * Hooking into WooCommerce.
         */
        add_action('woocommerce_before_checkout_billing_form', array($this, 'before_checkout_billing_form'));
        add_filter('woocommerce_checkout_fields', array($this, 'checkout_fields'));
        add_action('woocommerce_checkout_order_processed', array($this, 'order_status_completed'), 10, 3); 

        /**
         * If testing enabled,
         * Do the testing phase functions.
         */
        if($this->testing){
            require_once( WOO_BDAY_DIR . '/inc/class-woo-bday-testing.php');
            $this->testing_suite = new Woo_Bday_Testing();
        }

        /**
         * Add 'coin-member' role.
         */
        require_once( WOO_BDAY_DIR . '/inc/class-woo-bday-users.php');
        $this->roles = new Woo_Bday_Users();

        /**
         * Add an admin page.
         */
        require_once( WOO_BDAY_DIR . '/inc/class-woo-bday-admin.php');
        $this->admin = new Woo_Bday_Admin();
    }

    /**
     * Checkout Fields
     * 
     * class-wc-checkout.php
     * $this->fields = apply_filters( 'woocommerce_checkout_fields', $this->fields );
     * 
     * https://woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
     */
    function checkout_fields($fields){
        /**
         * Change the checkout fields here.
         */


        $inserted = array('billing_birthday' => array(
            'label'     => __('Birthday', 'woo_bday'),
            'placeholder'   => _x('YYYY-MM-DD', 'placeholder', 'woo_bday'),
            'required'  => false,
            'class'     => array('form-row-wide'),
            'clear'     => true

            )
        );

        /**
         * Splice the fields to add the birthday.
         */
        $res = array_slice($fields['billing'], 0, 2, true) + $inserted + array_slice($fields['billing'], 2, count($fields['billing']) - 2, true);

        $fields['billing'] = $res;
        return $fields;
    }

    /**
     * Before checkout billing form
     * 
     * form-billing.php
     * <?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
     */
    function before_checkout_billing_form($checkout){
        // Do something before checkout form.
        // We have the checkout object.
    }

    function order_status_completed($order_id, $posted_data, $order){
        $first_name = $posted_data['billing_first_name'];
        $last_name = $posted_data['billing_last_name'];
        $email = $posted_data['billing_email'];
        $birthday = $posted_data['billing_birthday'];

        $user_name = $email;

        $user_id = username_exists( $user_name );
        
        if ( ! $user_id && false == email_exists( $email ) ) {
            $random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
            $user_id = wp_create_user( $user_name, $random_password, $email );

            /**
             * Remove default role and assign 'coin_member' role.
             */
            $user = new WP_User( $user_id );
            $user->remove_role( 'subscriber' );
            $user->add_role( 'coin_member' );

            /**
             * Insert the birthday as user_meta
             */
            add_user_meta( $user_id, '_woo_billing_bday', sanitize_text_field($birthday));

        } else {
            // User already exists.
        }
    }

    function activation(){
        /**
         * Activate testing functions.
         */
        if($this->testing){
            $this->testing_suite->activation();
        }
    }

    function deactivation(){
        /**
         * Revert the changes by testing functions.
         */
        if($this->testing){
            $this->testing_suite->deactivation();
        }
    }
}
