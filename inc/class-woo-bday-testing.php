<?php
/**
 * This class adds data for testing purposes.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Woo_Bday_Testing{
    /**
     * Test users
     */
    private $test_users;

    function __construct() {
        $this->test_users = [
            [ 'first_name' => 'First 1', 'last_name' => 'Last 1', 'email' => 'first1@last.com', 'birthday' => '1990-10-10'],
            [ 'first_name' => 'First 2', 'last_name' => 'Last 2', 'email' => 'first2@last.com', 'birthday' => '1986-09-09'],
        ];
    }

    /**
     * On Activation
     */
    function activation(){

        $new_users = $this->test_users;

        foreach( $new_users as $new_user ){
            $user_name = $new_user['email'];
            $user_id = username_exists( $user_name );
            
            if ( ! $user_id && false == email_exists( $new_user['email'] ) ) {
                $random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );
                $user_id = wp_create_user( $user_name, $random_password, $new_user['email'] );

                /**
                 * Remove default role and assign 'coin_member' role.
                 */
                $user = new WP_User( $user_id );
                $user->remove_role( 'subscriber' );
                $user->add_role( 'coin_member' );

                /**
                 * Add birthday as user meta.
                 * 
                 * https://developer.wordpress.org/reference/functions/add_user_meta/
                 */
                add_user_meta( $user_id, '_woo_billing_bday', $new_user['birthday']);
            }
        }
    }

    /**
     * On Deactivation
     */
    function deactivation(){
        $current_users = $this->test_users;

        foreach( $current_users as $current_user ){
            $user_name = $current_user['email'];
            $user_id = username_exists( $user_name );
            if ( $user_id ) {
                $user_id = wp_delete_user( $user_id );
            }
        }

    }
}