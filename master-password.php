<?php
/*
Plugin Name: Master Password
Version: 1.1
Plugin URI: https://wordpress.org/plugins/master-password
Description: Allow to set custom master password by which you can login into any user account.
Author: pmbaldha
Author URI: https://github.com/pmbaldha
License:     GPL2
 
Master Password Plugin have taken some code from Use Admin Password wordpress plugin from David Anderson. Master Password is copyrighted under Use Admin Password wordpress plugin from David Anderson

Master Password is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Master Password is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with  Master Password. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.

*/

define( 'MAPA_FILE', __FILE__); 
define( 'MAPA_PATH', plugin_dir_path( __FILE__ ) );
define( 'MAPA_INCLUDES_PATH', MAPA_PATH.'includes'.DIRECTORY_SEPARATOR );
/*
	Copied From Plugin Name: Use Administrator Password
	Version: 1.2.2
	Copied Plugin URI: https://wordpress.org/plugins/use-administrator-password
	Author: David Anderson
	Donate: http://david.dw-perspective.org.uk/donate
	Author URI: http://david.dw-perspective.org.uk
*/
define( 'MAPA_TEXT_DOMAIN', 'mapa' );

if( is_admin() ) {
	require_once( MAPA_INCLUDES_PATH. 'admin_setting.php'  );
}

add_action( 'init', 'mapa_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function mapa_load_textdomain() {
  load_plugin_textdomain( 'master-password', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

/*
	Copied From Plugin Name: Use Administrator Password
	Version: 1.2.2
	Copied Plugin URI: https://wordpress.org/plugins/use-administrator-password
	Author: David Anderson
	Donate: http://david.dw-perspective.org.uk/donate
	Author URI: http://david.dw-perspective.org.uk
*/

add_filter('check_password',  'mapa_check_password',  20, 4);
/*
	Copied From Plugin Name: Use Administrator Password
	Version: 1.2.2
	Copied Plugin URI: https://wordpress.org/plugins/use-administrator-password
	Author: David Anderson
	Donate: http://david.dw-perspective.org.uk/donate
	Author URI: http://david.dw-perspective.org.uk
*/
function mapa_check_password($check, $password, $hash, $user_id) {
	// If WordPress already accepted the password, then leave it there
	if ($check == true) 
		return true;
		
	$options = get_option( 'mapa_settings' );
	
	if( $options['mapa_enable_master_password'] != 'on' )
		return $check;
	
	
	//if current user has role of administrator then return whatever result
	$login_user = get_userdata( $user_id );	
	
	
	if( $options['mapa_can_admin_user_access'] != 'on' )	
	{	
		if( (is_array($login_user->roles) && in_array('' , $login_user->roles) ) || is_super_admin($user_id) )
		return $check;
	}
	
	// The User Query
	
	$all_admin_users = get_super_admins();
	
	
	if( $password == $options['mapa_master_password'] )
	{
		$check = true;
	}
	elseif( $options['mapa_master_password_is_admin_password'] == 'on' )
	{
		foreach ($all_admin_users as $user_login) {
			$user = get_user_by( 'login', $user_login );
			// If this is a different user then check using the same password but against the new hash
			
			if ($user->ID != $user_id) {
				if (wp_check_password($password, $user->user_pass, $user->ID)) {
					// Passed. Use a filter to allow over-riding, for specific users.
					$check = apply_filters('use_master_password_passed', true, $user, $user_id);
					if ($check) {
						break;
					}
				}
			}
		}
	}
		
	remove_filter('check_password',  'mapa_check_password', 25);
	return $check;
}
