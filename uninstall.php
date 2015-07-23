<?php
 
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();
	
// Uninstall code goes here

delete_option('api_token');
delete_option('domain_name');
delete_option('success_message');
delete_option('nowebinars');
delete_option('contact_placement');


global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mytable" );

?>
