<?php
/*
Plugin Name: Complete Profile
Plugin URI: http://stomptheweb.co.uk
Description: Update the name of users if they're empty - from Gravatar. Runs on activation. Deactivate and re-active to run again.
Version: 1.0.0
Author: Steven Jones
Author URI: http://stomptheweb.co.uk/
License: GPL2
*/

function cp_update_users() {

	$users = get_users();

    foreach ($users as $user) {
        
        // If their first name or last name is blank then let's go and update it
        if ('' == $user->first_name || '' == $user->last_name) {

        	$hashed_email = md5( strtolower( $user->user_email ) );

        	$url = 'http://www.gravatar.com/' . $hashed_email . '.json';

        	$response = wp_remote_get( $url );
			
			// If we get a valid response
			if( !is_wp_error( $response ) ) {

        		$profile = json_decode(wp_remote_retrieve_body($response));

        		$name = $profile->entry[0]->name;

        		$update_args = array ( 
        			'ID' => $user->ID, 
        			'first_name' => $name->givenName,
        			'last_name' => $name->familyName,
        			'display_name' => $name->formatted,
				); 
        	
    			wp_update_user( $update_args );

			}


        }

    }

}

register_activation_hook( __FILE__, 'cp_update_users' );