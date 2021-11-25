<?php
/**
 * This class for managing roles.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Woo_Bday_Users{

    function __construct() {
        add_action( 'init', array($this, 'coin_members') );
        add_filter( 'manage_users_columns', array($this, 'manage_users_columns') );
        add_filter( 'manage_users_custom_column', array($this, 'manage_users_custom_column'), 10, 3 );
    }

    /**
     * Add new role.
     * 
     * https://developer.wordpress.org/reference/functions/add_role/
     */
    function coin_members(){
        add_role( 'coin_member', 'Coin Member', array( 'read' => true, 'level_0' => true ) );
    }

    /**
     * Add a birthday column.
     */
    function manage_users_columns( $column ){
        $column['bday'] = 'Birthday';
        return $column;
    }
    
    /**
     * Manage custom column data.
     */
    function manage_users_custom_column( $val, $column_name, $user_id ) {
        switch ($column_name) {
            case 'bday' :
                $meta = get_user_meta( $user_id ,'_woo_billing_bday' );
                if( $meta ){
                    return $meta[0];
                } else {
                    return "N/A";
                }
            default:
        }
        return $val;
    }
}

