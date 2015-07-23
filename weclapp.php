<?php
/*
Plugin Name: weclapp 
Plugin URI: 
Version: 1.0
Author: <a href="http://www.weclapp.com"> weclapp GmbH </a>
Text Domain: weclapp
Domain Path: /languages
Description: This plugin integrates weclapp functionality into wordpress CMS
*/

//weclapp REST API integration
include( "includes/wc-send.php" );

add_action( 'admin_menu', 'add_plugin_page'  );
add_action( 'admin_init', 'page_init' );

function weclapp_load_plugin_textdomain() {
    load_plugin_textdomain( 'weclapp', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'weclapp_load_plugin_textdomain' );

/**
*Add options page
*/
function add_plugin_page()
{
	add_options_page(
		'weclapp Settings', 
		'weclapp', 
		'manage_options', 
		'weclapp-settings', 
		'create_admin_page' 
	);
}

/**
*Options page callback
*/
function create_admin_page()
{
	// Set class property
	weclapp_get_option("api_token");
	weclapp_get_option("domain_name");
	weclapp_get_option("contact_placement");
	weclapp_get_option("success_message");
	weclapp_get_option("nowebinars");
	
			// This prints out all hidden setting fields
	?> 
	<div class="wrap">
		<h2><?php esc_html_e("weclapp Einstellungen","weclapp");?></h2>
		<form method="post" action="options.php">
		<?php
			settings_fields( 'weclapp_options' );   
			do_settings_sections( 'weclapp-settings' );
			submit_button(); 
		?>
		</form>
	</div>
	<?php
}

/**
*Register and add settings
*/
function page_init()
{        
	register_setting(
		'weclapp_options', // Option group
		'api_token', // Option name
		'sanitize_input'  // Sanitize
	);
	
	register_setting(
		'weclapp_options', // Option group
		'domain_name', // Option name
		'sanitize_input'  // Sanitize
	);

	register_setting(
		'weclapp_options', // Option group
		'contact_placement', // Option name
		'sanitize_input' // Sanitize
	);
	
	register_setting(
		'weclapp_options', // Option group
		'display_form', // Option name
		'sanitize_input'  // Sanitize
	);
	
	register_setting(
		'weclapp_options', // Option group
		'success_message', // Option name
		'sanitize_input'  // Sanitize
	);
	
	register_setting(
		'weclapp_options', // Option group
		'nowebinars', // Option name
		'sanitize_input'  // Sanitize
	);
	
	
	add_settings_section(
		'neccessary_section', // ID
		__('Notwendige Einstellungen','weclapp'), // Title
		'print_neccessary_section_info' , // Callback
		'weclapp-settings' // Page
	);  
	
	add_settings_section(
		'non_neccessary_section', // ID
		__('Nicht notwendige Einstellungen','weclapp'), // Title
		'print_non_neccessary_section_info', // Callback
		'weclapp-settings' // Page
	);  

	add_settings_field(
		'api_token', // ID
		__('API Token','weclapp'), // Title 
		'api_token_callback', // Callback
		'weclapp-settings', // Page
		'neccessary_section' // Section           
	);     

	add_settings_field(
		'domain_name', // ID
		__('Domain Name','weclapp'), // Title 
		'domain_name_callback' , // Callback
		'weclapp-settings', // Page
		'neccessary_section' // Section           
	);      
	add_settings_field(
		'contact_placement', // ID
		__('Neue Benutzer anlegen als','weclapp'), // Title 
		'contact_placement_callback', // Callback
		'weclapp-settings', // Page
		'neccessary_section' // Section           
	);  
	add_settings_field(
		'display_form', // ID
		__('Formular anzeigen','weclapp'), // Title 
		'display_form_callback', // Callback
		'weclapp-settings', // Page
		'neccessary_section' // Section           
	);  
	add_settings_field(
		'success_message', // ID
		__('Benutzerdefinierte Erfolgsmeldung','weclapp'), // Title 
		'success_message_callback', // Callback
		'weclapp-settings', // Page
		'non_neccessary_section' // Section           
	); 
	add_settings_field(
		'nowebinars_message', // ID
		__('Benutzerdefinierte Mitteilung, falls kein Webinar ansteht','weclapp'), // Title 
		'nowebinars_callback', // Callback
		'weclapp-settings', // Page
		'non_neccessary_section' // Section           
	);  

}
/**
*sanitize settings 
**/
function sanitize_input( $input ) {
	$input = strip_tags( stripslashes( $input ) );
	$input = sanitize_text_field( $input );
	return $input;
}

/**
*Print the section text
**/
function print_neccessary_section_info()
{
	_e('Bitte geben Sie Ihren weclapp API Token sowie Ihren Domain-Namen ein.', 'weclapp');
}

function print_non_neccessary_section_info()
{
	_e('Hier können Sie bei Bedarf eigene Meldungen setzen.', 'weclapp');
}

/** 
*callback functions for options
*/
function api_token_callback()
{
	$setting = esc_attr( get_option( 'api_token' ) );
	echo "<input type='text' name='api_token' value='$setting' />";
}
function domain_name_callback()
{
	$setting = esc_attr( get_option( 'domain_name' ) );
	echo "<input type='text' name='domain_name' value='$setting' />.weclapp.com";
}
function success_message_callback()
{
	$setting = esc_attr( get_option( 'success_message' ) );
	echo "<textarea name='success_message' rows='5' cols ='100'>$setting</textarea>";
}
function nowebinars_callback()
{
	$setting = esc_attr( get_option( 'nowebinars' ) );
	echo "<textarea name='nowebinars' rows='5' cols ='100'>$setting</textarea>";
}
function contact_placement_callback()
{
	echo '<fieldset>
			<input type="radio" id="contact" name="contact_placement" value="contact" ' . ( ( 'contact' == weclapp_get_option( 'contact_placement' )) ? 'checked' : '' ). '><label for="contact">  Contact</label><br> 
			<input type="radio" id="lead" name="contact_placement" value="lead" ' . ( ( 'lead' == weclapp_get_option( 'contact_placement' )) ? 'checked' : ''). '><label for="lead">  Lead</label><br> 
			<input type="radio" id="customer" name="contact_placement" value="customer" ' . ( ( 'customer' == weclapp_get_option( 'contact_placement' )) ? 'checked' : '' ). '><label for="customer">  Customer</label> 
		</fieldset>';
} 

function display_form_callback()
{
	echo "<input type='checkbox' name='display_form' value='1'" . checked( 1, get_option( 'display_form', '1' ), false ) . ">";
}
/** 
* add settings link on plugin page
**/
function weclapp_settings_link( $links ) { 
  $settings_link = '<a href="options-general.php?page=weclapp-settings">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename( __FILE__ ); 
add_filter( "plugin_action_links_$plugin", 'weclapp_settings_link' );

/*
**used to reduce the number of displayed webinars to the number of upcoming webinars with one hour postponement
*/
function weclapp_display_webinar_til() 
{
	$currentTime = ( time() * 1000 );
	//offset in milliseconds
	$timeOffset = 3600000;
	$displayTill = $currentTime + $timeOffset;
	return $displayTill;
}

/**
*api call to display upcoming webinars as well as a formular
**/
function weclapp_api() 
{

	date_default_timezone_set( 'Europe/Berlin' );
	$args = array(
		'headers' => array(
			'Authorization' => 'Basic ' . base64_encode( "*" . ':' . weclapp_get_option("api_token") )
		)
	);
	//get all upcoming webinars 
	$result = wp_remote_retrieve_body( wp_remote_get( 'https://'.weclapp_get_option("domain_name").'.weclapp.com/webapp/api/v1/campaign/?campaignType-eq=WEBINAR&campaignStartDate-gt='.weclapp_display_webinar_til(), $args ));
	$result = json_decode( $result, true );
	$result = $result['result'];
	//if there are no upcoming webinars, display a custom webinar message or the default one, respectively
	if ( empty( $result )) {		
		$content = weclapp_get_option( "nowebinars" );
	} else {
		$content = '<div class="webinar-container">';
		//display a webinar box for each upcoming webinar
		foreach ( $result as &$val ) {
			$content .= '<div class=webinar-box>';
				$content .= '<div class="webinar-head" >';

					$content .= '<div class="webinar-checkbox"'. ( ( 1 == get_option( 'display_form' )) ? '' : 'style="display: none"' ) . '><input type="hidden" name="webinare" data-weclapp-campaign-id="' . $val['id'] . '" /></div>';
				
					$content .= '<div class="webinar-headline">';
						$content .= '<h3 style="margin: 0px !important;padding: 0px !important;">' . $val['campaignName'] . '</h3>';
						$duration = ( $val['campaignEndDate']/1000 - $val['campaignStartDate']/1000 ) / 60;
						$content .= '<p>' . sprintf(__( 'Dauer ca. %1$s Min. | Nächster Termin: %2$s Uhr', 'weclapp' ), $duration, date("d. m. Y, H.i", $val['campaignStartDate']/1000)) . '</p>';
					$content .= '</div>';

					$content .= '<div class="webinar-arrow"></div>';

				$content .= '</div>';
				$content .= '<div class="webinar-content"><h3>' . __("Inhalt",  "weclapp") . '</h3>'.$val['description'].'</div>';
			$content .= '</div>';
		}
		$content .= '</div>';
		//input formular, submit buttons calls registerUser in weclapp.js (AJAX-call)
		if ( '1' == get_option( 'display_form', '1' )) {
			$content .= '
				<div id="name-group" class="form-group">
					<label for="weclapp-name">' . __("Name", "weclapp") . '</label> 
					<input id="weclapp-name" name="wc_name" type="text" value="Hans Mueller" class="form-control" />
				</div>
				<div id="email-group" class="form-group">
					<label for="weclapp-email"> E-Mail </label> 
					<input id="weclapp-email" name="wc_email" type="text" value="hans@aol.com" class="form-control" />
				</div>
				<div id="phone-group" class="form-group">
					<label for="weclapp-phone">' . __("Telefonnummer", "weclapp") . '</label> 
					<input id="weclapp-phone" name="wc_phone" type="text" value="1234" class="form-control" />
				</div>
				<p id = "demo"></p>
				<input type="submit" name="submit" id="submitbutton" value="'. __("Anmelden","weclapp") . '" onclick="registerUser()" />';
			$content .='<div style="padding-top: 20px;padding-bottom: 20px;">';
			$content .='<div id="loader" style="display: none;"> <img src="' . plugin_dir_url(  __FILE__  ) . 'assets/icons/ajax-loader.gif"> </div>';
			$content .='<div id="errorbox" class="weclapp-error-message" style="display: none;"><span></span></div>';
			$content .='<div id="successbox" class="weclapp-success-message" style="display: none;"><span></span></div>';
		}	
	}
	return $content;
}

function weclapp_register_shortcodes() 
{
    add_shortcode( 'registerWebinar', 'registerWebinar');
    add_shortcode( 'weclappWebinar', 'weclapp_api' );

}

/**
*add weclapp.js (for AJAX-call) and weclapp.css
**/
function weclapp_add_scripts()
{
	wp_enqueue_style( 'weclapp-style', plugin_dir_url( __FILE__ ) . 'css/weclapp.css' );
    wp_enqueue_script( 'ajax_custom_script', plugin_dir_url( __FILE__ ) . 'js/weclapp.js', array('jquery') );
    wp_localize_script( 'ajax_custom_script', 'frontendajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
}

add_action( 'wp_enqueue_scripts', 'weclapp_add_scripts' );

//AJAX-call in weclapp.js, associated functions in wcsend.php
add_action( 'wp_ajax_nopriv_weclapp_campaign_register', 'weclapp_campaign_register' );
add_action( 'wp_ajax_weclapp_campaign_register', 'weclapp_campaign_register' );

add_action( 'init', 'weclapp_register_shortcodes');

/**
*retrieves the value of a particular option and possibly sets a default value, if the option hasn´t been defined yet
**/
function weclapp_get_option( $name )
{
	$optionValue = esc_attr( get_option( $name ) );
	if( null == $optionValue ){
		$optionValue = null;
		switch($name)
		{
			case "api_token": 
				$optionValue = "12346789";
				update_option( "api_token", $optionValue );
				break;
			case "domain_name":
				$optionValue = "your_domain_name";
				update_option( "domain_name", $optionValue );
				break;
			case "success_message":
				$optionValue = "Sie wurden erfolgreich am Webinar angemeldet!";
				update_option( "success_message", $optionValue );
				break;
			case "contact_placement":
				$optionValue = "contact";
				update_option( "contact_placement", $optionValue );
				break;
			case "nowebinars":
				$optionValue = "In der nächsten Zeit sind noch keine Webinare angesetzt. Schauen Sie später nochmal vorbei!";
				update_option( "nowebinars", $optionValue );
		}		
	}
	return $optionValue;
}

?>
