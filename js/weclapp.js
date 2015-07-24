function registerUser( campaignType ) {

	//create an array for all selected campaigns
	campaignIds = [];
	//fill that array 
	jQuery( 'input[name=checkbox_' + campaignType + '][value=1]' ).each( function( index, elem ){
		campaignIds.push( jQuery( elem ).data( 'weclapp-campaign-id' ));
	} );
	json_campaignIds = JSON.stringify( campaignIds );
	var formData = {
		'action': 'weclapp_campaign_register',
		'name'              : jQuery( 'input[name=wc_name_' + campaignType  + ']' ).val(),
		'email'             : jQuery( 'input[name=wc_email_'  + campaignType  + ']' ).val(),
		'phone'    			: jQuery( 'input[name=wc_phone_'  + campaignType + ']' ).val(),
		'campaignIds' 		: json_campaignIds,
	};
	//hide all message boxes and remove the red border, if existing
	jQuery( '#errorbox_' + campaignType).hide();
	jQuery( '#successbox_' + campaignType).hide();
	jQuery( '.form-group' ).removeClass( 'has-error' ); // remove the error class
	//empty error box
	jQuery( '#errorbox_' + campaignType + ' span' ).empty();
	//show loader animation
	jQuery('#loader_' + campaignType).show();
	jQuery.ajax( {
		method        : 'POST', 
		url         : frontendajax.ajaxurl,
		data        : formData, 
		processData : true,
		success: function( data ) {
		data = JSON.parse( data );
			//hide the loader animation
			jQuery('#loader_' + campaignType).hide();
			 if ( data.errors ) {
				//show errorbox for displaying errors
				jQuery( '#errorbox_'  + campaignType ).show();
				// display errors for name
				if ( data.errors.name ) {
					jQuery( '#name-group_'  + campaignType ).addClass( 'has-error' ); // add the error class to show red input
					jQuery( '#errorbox_'  + campaignType  + ' span' ).append( data.errors.name ); // add the error message 
				}

				// display errors for email 
				if (  data.errors.email ) {
					jQuery( '#email-group_'  + campaignType ).addClass( 'has-error' ); // add the error class to show red input
					jQuery( '#errorbox_'  + campaignType  + ' span' ).append( data.errors.email ); // add the error message 
				}
				// display errors for phone 
				if ( data.errors.phone ) {
					jQuery( '#phone-group_'  + campaignType ).addClass( 'has-error' ); // add the error class to show red input
					jQuery( '#errorbox_'  + campaignType  + ' span' ).append( data.errors.phone ); // add the error message 
				}
				// display errors for campaign
				if ( data.errors.campaignIds ) {
					jQuery( '#errorbox_'  + campaignType + ' span' ).append( data.errors.campaignIds ); // add the error message 
				}
				// display wordpress errors
				if ( data.errors.wp_remote_request ) {
					jQuery( '#errorbox_'  + campaignType  + ' span' ).append( data.errors.wp_remote_request ); // add the error message 
				}
				// display weclapp errors
				if ( data.errors.weclappApi ) {
					jQuery( '#errorbox_'  + campaignType  + ' span' ).append( data.errors.weclappApi ); // add the error message 
				}
			} else {
					//show success message
					jQuery( '#successbox_'  + campaignType  + ' span' ).html( data.message );
					jQuery( '#successbox_'  + campaignType).show();
			  }
		}
	} ).fail( handleError );

};
	
function handleError( error ){
	jQuery('#loader').hide();
	console.log( error );
}

jQuery( document ).ready( function(  ) {	

	//display campaign description on header-click
	jQuery( ".webinar-container .webinar-head" ).on(  "click", function(  event  ) {
	    jQuery( this ).parent( ".webinar-box" ).toggleClass( "active" ).find( ".webinar-content" ).slideToggle();
	    jQuery( this ).parent( ".webinar-box" ).siblings( ".active" ).removeClass( "active" ).find( ".webinar-content" ).slideUp();
	} );

	//change checkbox image on click
	jQuery( ".webinar-container .webinar-checkbox" ).on( "click", function( event ) {
		event.preventDefault();
		event.stopPropagation();
		if ( jQuery( this ).find( 'input[type="hidden"]' ).val(  ) == 0 ) {
			jQuery( this ).addClass( "active" );
			jQuery( this ).find( 'input[type="hidden"]' ).val( 1 );
		} else {
			jQuery( this ).removeClass( "active" );
			jQuery( this ).find( 'input[type="hidden"]' ).val( 0 );
		}
	} );
} ); 
