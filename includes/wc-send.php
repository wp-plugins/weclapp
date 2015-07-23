<?php
	function weclapp_campaign_register()
	{
		//array for errors, success state and success message 
		$data = array(
			'message' => '',
		);
		//default header for api-calls
		$api_header = array(
			'Content-type'  => 'application/json', 
			'Authorization' => 'Basic ' . base64_encode( '*' . ':' . weclapp_get_option( "api_token" ) )
		);
		//check all fields passed by AJAX-call
		$name = weclapp_clean_input( $_POST["name"] );
		if ( empty( $name )) {
			$data['errors']['name'] = __( "Bitte geben Sie Ihren Namen ein", "weclapp" ) . "<br />";
		} else {
			//call function weclapp_clean_input for cleaning the passed input
			if( !preg_match( '/\s/', $name )) {
				$data['errors']['name'] = __( "Bitte geben Sie Ihren Vor -und Nachnamen ein", "weclapp" ) . "<br />";
			}
		}
		$email = weclapp_clean_input( $_POST["email"] );
		if ( empty( $email )) {
			$data['errors']['email'] = __( "Bitte geben Sie Ihre Email-Adresse ein", "weclapp" ) . "<br />";
		} else {
			if ( !is_email( $email )) {
				$data['errors']['email'] = __( "Bitte geben Sie eine gültige Email-Adresse ein", "weclapp" ) . "<br />";
			}
		}
		$phone = weclapp_clean_input( $_POST["phone"] );
		if ( empty( $phone )) {
			$data['errors']['phone'] = __( "Bitte geben Sie Ihre Telefonnummer ein", "weclapp" ) . "<br />";
		} else {
			if ( !ctype_digit( $phone )) {
				$data['errors']['email'] = __( "Bitte geben Sie eine gültige Telefonnummer ein", "weclapp" ) . "<br />";
			}
		}
		//remove unwanted characters from campaignIds-array
		$tempCampaignIds = str_replace("\\", "", $_POST["campaignIds"]);
		$campaignIds = json_decode($tempCampaignIds);
		if( empty( $campaignIds )) {
			$data['errors']['campaignIds'] = __( "Bitte wählen Sie eine Kampagne", "weclapp") . "<br />";
		}
		if( empty( $data['errors'] )) {
			//register user for each selected campaign
			foreach( $campaignIds as $check ) {
				weclapp_single_campaign_register( $check, $name, $email, $phone, $api_header, $data );
			}
		}
		echo json_encode( $data );
		die();
	}
	/**
	*standard api_header for weclapp api calls
	*data for errors
	**/
	function weclapp_single_campaign_register( $campaignId, $name, $email, $phone, $api_header, &$data )
	{
		// try if e-mail is already participating in any of the wanted campaigns
		$campaignName = weclapp_check_campaign( $campaignId, $email, $api_header, $data );
		if($campaignName != null){
			$data['message'] .= __( "Sie sind unter Ihrer E-Mail-Adresse für diese Kampagne bereits angemeldet: ", "weclapp" ) . $campaignName . " <br />";
		} else
		{
			//weclapp_contact_exists returns partyId if the contact exists or null, otherwise
			$partyId = weclapp_contact_exists( $email, $api_header, $data );
			//e-mail-address is not a contact, customer or lead, so we place him as a contact
			if($partyId == null)
			{
				$partyId = weclapp_create_contact( $name, $email, $phone, $api_header, $data );
			}		
			//call the api for actual campaign registration 
			$arg = array ( 
				'body'    => json_encode( array( 
					'campaignId' => $campaignId, 
					'partyId'    => $partyId )),
				'headers' => $api_header,
			);
			$response = wp_remote_post ( "https://" . weclapp_get_option( "domain_name" ) . ".weclapp.com/webapp/api/v1/campaignParticipant" , $arg );
			if ( empty( $data['errors'] )) {
				$data['success'] = true;
				//if the user changed the default success message, this one will displayed
				$success_message = weclapp_get_option( "success_message" );
				$data['message'] .= $success_message ."<br />";
			} else {
				// success state notifies AJAX-call of errors to be displayed
				$data['success'] = false;	
			}
		}
	}
	function weclapp_clean_input( $input ) 
	{
		$input = trim( $input );
		$input = stripslashes( $input );
		$input = htmlspecialchars( $input );
		$input = sanitize_text_field( $input );
		return $input;
	}
	/**
	*returns campaign name if the user is already in the campaign, otherwise null
	**/
	function weclapp_check_campaign( $campaign, $email, $api_header, &$data )
	{
		//arguments for api-call
		$args = array(
			'headers' => $api_header,
		);
		$result = wp_remote_get( "https://" . weclapp_get_option( "domain_name" ) . ".weclapp.com/webapp/api/v1/campaignParticipant?campaignId-eq=" . $campaign . "&party.email-ieq=" . $email, $args );
		//call function to check for errors
		$result = weclapp_api_check( $result, $data );
		//if user already participates, return campaign name 
		if (isset($result) && isset($result['result']) && isset($result['result'][0])) {
			$result2 = wp_remote_get( "https://" . weclapp_get_option( "domain_name" ) . ".weclapp.com/webapp/api/v1/campaign/?campaignNumber-eq=" . $result['result']['0']['campaignNumber'], $args );
			$result2 = weclapp_api_check( $result2, $data);
			return $result2['result']['0']['campaignName'];
		}
		return null;
	}
	/**
	*returns party Id if the user is already in the contact list, otherwise null
	**/
	function weclapp_contact_exists( $email, $api_header, &$data )
	{
		//is there a contact with this email?
		$args = array(
			'headers' => $api_header
		);
		$result = wp_remote_get("https://".weclapp_get_option("domain_name").".weclapp.com/webapp/api/v1/contact?email-eq=".$email,$args);
		$result = weclapp_api_check( $result, $data );
		//if no, is there a lead with this email?
		if ( !isset( $result['result']['0'] ) ) {
			$result = wp_remote_get("https://".weclapp_get_option("domain_name").".weclapp.com/webapp/api/v1/customer?email-eq=".$email,$args);
			$result = weclapp_api_check( $result, $data );
		}
		//if no, is there a customer with this email?
		if ( !isset( $result['result']['0'] ) ) {
			$result = wp_remote_get("https://".weclapp_get_option("domain_name").".weclapp.com/webapp/api/v1/lead?email-eq=".$email, $args);
			$result = weclapp_api_check( $result, $data );
		}
		//return null, if the email is neither contact, nor lead or customer 
		if ( !isset( $result['result']['0'] ) ) {
			return null;
		}
		//error_log(json_encode($result), 3, "C:\\Bitnami\\wordpress-4.2.2-0\\apache2\\logs\\error.log");
		//return partyId otherwise
		else {
			$partyId = $result['result']['0']['id'];
			return $partyId;
		}		
	}
	function weclapp_create_contact( $name, $email, $phone, $api_header, &$data )
	{
		//divide name into first name and surname
		$name = preg_split( '/ (?!.* )/', $name );
		$params = array( 
			'body'     => json_encode( array(
				"lastName"  => $name[1], 
				"firstName" => $name[0], 
				"email"     => $email, 
				"partyType" => "PERSON", 
				"company"   => "foo", 
				"phone"     => $phone,
				)),
			'headers'  => $api_header,
		);
		//create the contact in the specified list (contact, lead or customer)
		$result = wp_remote_post( "https://" . weclapp_get_option( "domain_name" ) . ".weclapp.com/webapp/api/v1/" . weclapp_get_option("contact_placement"), $params );
		$result = weclapp_api_check( $result, $data );
		$partyId = $result['id'];
		return $partyId;		
	}
	/**
	*check api response for server-side errors or Wordpress HTTP errors
	**/
	function weclapp_api_check( $result, &$data )
	{
		//check for errors caused by Wordpress HTTP API
		if ( !weclapp_wordpress_error( $result, $data ))
		{
			//check for server-side errors
			if($result['response']['code'] == 404)
				$data['errors']['weclappApi'] = __( "Fehler 404: Ungültiger Domain-Name. Bitte kontaktieren Sie den Webmaster", "weclapp" );
			if($result['response']['code'] == 401)
				$data['errors']['weclappApi'] =  __( "Fehler 401: Ungültiger API Token. Bitte kontaktieren Sie den Webmaster", "weclapp" );
			$result = $result['body'];
		} else {
			$result = null;
		}
		return json_decode( $result, true );
	}
	function weclapp_wordpress_error( $result, &$data )
	{
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
			$data['errors']['wp_remote_request'] = __( "Wordpress HTTP Fehler. Bitte kontaktieren Sie den Webmaster" ) . $error_message . "<br />";
			$data['success'] = false;
			return true;
		}
		return false;
	}
?>