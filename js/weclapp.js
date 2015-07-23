function registerUser(  ) {
	// get the form data
	// there are many ways to get this data using jQuery ( you can use the class or id also )

	campaignIds = [];
	jQuery( 'input[name=webinare][value=1]' ).each( function( index, elem ){
		campaignIds.push( jQuery( elem ).data( 'weclapp-campaign-id' ));
	} );
	json_campaignIds = JSON.stringify( campaignIds );
	var formData = {
		'action': 'weclapp_campaign_register',
		'name'              : jQuery( 'input[name=wc_name]' ).val(),
		'email'             : jQuery( 'input[name=wc_email]' ).val(),
		'phone'    			: jQuery( 'input[name=wc_phone]' ).val(),
		'campaignIds' 		: json_campaignIds,
	};
	jQuery( '#errorbox' ).hide();
	jQuery( '#successbox' ).hide();
	jQuery( '.form-group' ).removeClass( 'has-error' ); // remove the error class
	jQuery( '#errorbox span' ).empty();
	jQuery('#loader').show();
	jQuery.ajax( {
		method        : 'POST', // define the type of HTTP verb we want to use ( POST for our form )
		url         : frontendajax.ajaxurl,
		data        : formData, // our data object
		processData : true,
		success: function( data ) {
		data = JSON.parse( data );
			jQuery('#loader').hide();
			 if ( data.errors ) {
				jQuery( '#errorbox' ).show();
				// handle errors for name ---------------
				if ( data.errors.name ) {
					jQuery( '#name-group' ).addClass( 'has-error' ); // add the error class to show red input
					jQuery( '#errorbox span' ).append( data.errors.name ); // add the actual error message input
				}

				// handle errors for email ---------------
				if (  data.errors.email ) {
					jQuery( '#email-group' ).addClass( 'has-error' ); // add the error class to show red input
					jQuery( '#errorbox span' ).append( data.errors.email ); // add the actual error message under input
				}
				// handle errors for phone ---------------
				if ( data.errors.phone ) {
					jQuery( '#phone-group' ).addClass( 'has-error' ); // add the error class to show red input
					jQuery( '#errorbox span' ).append( data.errors.phone );
				}
				// handle errors for campaign ---------------
				if ( data.errors.campaignIds ) {
					jQuery( '#errorbox span' ).append( data.errors.campaignIds );
				}
				if ( data.errors.wp_remote_request ) {
					jQuery( '#errorbox span' ).append( data.errors.wp_remote_request );
				}
				if ( data.errors.weclappApi ) {
					jQuery( '#errorbox span' ).append( data.errors.weclappApi );
				}
			} else {
				// ALL GOOD! just show the success message!
				//if (  !jQuery.isEmptyObject(  )  )
				//{
					jQuery( '#successbox span' ).html( data.message );
					jQuery( '#successbox' ).show();
				//}
			  }
		}
	} ).fail( handleError );

};
	
function handleError( error ){
	jQuery('#loader').hide();
	console.log( error );
}

jQuery( document ).ready( function(  ) {		
	  jQuery( ".webinar-container .webinar-checkbox" ).on(  "click", function( event ) {

	  if ( jQuery( this ).find( 'input[type="hidden"]' ).val(  ) != 0 ) {
		jQuery( this ).removeClass( "active" );
		jQuery( this ).find( 'input[type="hidden"]' ).val( 1 );
	  } else {
		jQuery( this ).addClass( "active" );
		jQuery( this ).find( 'input[type="hidden"]' ).val( 0 );
	  }
	  event.preventDefault(  );
	  event.stopPropagation(  );

	} );

	jQuery( ".webinar-container .webinar-head" ).on(  "click", function(  event  ) {
	  jQuery( this ).parent( ".webinar-box" ).toggleClass( "active" ).find( ".webinar-content" ).slideToggle(  );
	  jQuery( this ).parent( ".webinar-box" ).siblings( ".active" ).removeClass( "active" ).find( ".webinar-content" ).slideUp(  );
	} );


	jQuery( ".webinar-container .webinar-checkbox" ).on(  "click", function( event ) {
	  event.preventDefault(  );
	  event.stopPropagation(  );
	  if ( jQuery( this ).find( 'input[type="hidden"]' ).val(  ) == 0 ) {
		jQuery( this ).addClass( "active" );
		jQuery( this ).find( 'input[type="hidden"]' ).val( 1 );
	  } else {
		jQuery( this ).removeClass( "active" );
		jQuery( this ).find( 'input[type="hidden"]' ).val( 0 );
	  }

	} );
} ); 