<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * This class adds a separate setting page.
 */
class Woo_Bday_Admin{
    function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu') );
    }

    function admin_init() {
        register_setting('woo_bday_admin', 'woo_bday_admin_settings');
    
        add_settings_section(
            'woo_bday_admin_settings_testing',
            'Upcoming Birthdays', array( $this, 'woo_bday_admin_settings_testing_callback'),
            'woo_bday_admin'
        );
    }

    function woo_bday_admin_settings_testing_callback() {
        echo '<p>Upcoming birthdays for WooCommerce customers.</p>';
        echo $this->birthday_table();
    }

    /**
     * Delete Settings.
     */
    function delete_settings(){
        delete_option('woo_bday_admin_settings');
    }

    /**
     * Add the top level menu page.
     */
    function admin_menu() {
        add_menu_page(
            'WooCommerce Birthdays',
            'Woo Birthdays',
            'manage_options',
            'woo_bday_admin',
            array($this, 'woo_bday_admin_options_page_html'),
            'dashicons-embed-photo',
            2
        );
    }

    /**
     * Top level menu callback function
     */
    function woo_bday_admin_options_page_html() {
        // check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
    
        // add error/update messages
    
        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if ( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'woo_bday_admin_messages', 'woo_bday_admin_message', __( 'Settings Saved', 'woo_bday_admin' ), 'updated' );
        }
    
        // show error/update messages
        settings_errors( 'woo_bday_admin_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                        // output security fields for the registered setting "woo_bday_admin"
                        settings_fields( 'woo_bday_admin' );
                        // output setting sections and their fields
                        // (sections are registered for "woo_bday_admin", each field is registered to a specific section)
                        do_settings_sections( 'woo_bday_admin' );
                        // output save settings button
                        submit_button( 'Send Email' );
                        ?>
            </form>
        </div>
        <?php
    }

    /**
     * Output Birthday Table.
     */
    function birthday_table(){
        /**
         * Get all users.
         * 
         * https://developer.wordpress.org/reference/functions/get_users/
         */
        $users = get_users( array( 'role__in' => array( 'coin_member' ) ) );
        echo '<table class="widefat fixed" cellspacing="0">
            <thead>
            <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col">ID</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
                    <th id="columnname" class="manage-column column-columnname num" scope="col">Birthday</th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col">ID</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
                    <th id="columnname" class="manage-column column-columnname num" scope="col">Birthday</th>
            </tr>
            </tfoot>

            <tbody>';
        foreach ( $users as $user ) {

            echo '<tr>';
            $meta = get_user_meta( $user->ID ,'_woo_billing_bday' );
            echo '<th>' . esc_html( $user->ID ) . '</th>';
            echo '<th>' . esc_html( $user->display_name ) . '</th>';
            echo '<th>' . esc_html( $meta[0] ) . '</th>';
            echo '</tr>';

        }

        echo '</tbody></table>';
    }
}