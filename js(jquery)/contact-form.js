jQuery( document ).ready( function( $ ) {

	var langErrorDialogs = {
		badEmail: hotelwp_contact_form_text.invalid_email,
		requiredFields: hotelwp_contact_form_text.required_field,
		groupCheckedTooFewStart: hotelwp_contact_form_text.required_field + '<span style="display: none">',
		groupCheckedEnd: '</span>',
		badInt: hotelwp_contact_form_text.invalid_number
	};

	$.validate({
		form: '.hotelwp-contact-form',
		validateOnBlur: false,
		language: langErrorDialogs,
		borderColorOnError: false,
		scrollToTopOnError: false,
		onError: function( $form ) {
			$form.find( '.hotelwp-cf-submit input' ).blur();
			var page_padding_top = 0;
			if ( $( '.header' ).hasClass( 'is-fixed-header' ) ) {
				page_padding_top = $( '.header' ).height();
			}
			if ( $( '#wpadminbar' ).length ) {
				page_padding_top += $( '#wpadminbar' ).height();
			}
			$( 'layout, body' ).animate({	scrollTop: $( 'p.has-error' ).first().offset().top - page_padding_top }, 400 );
		},
		onSuccess: function( $form ) {
			submit_contact_form( $form );
			return false;
		}
	});

	function submit_contact_form( $form ) {
		$form.find( '.hotelwp-cf-error, .hotelwp-cf-msg-send' ).slideUp();
		$form.find( 'input[type="submit"]' ).blur();
		var policies_error = '';
		if (
			$form.find( 'input[name="hotelwp_cf_field_terms_and_cond"]' ).length &&
			! $form.find( 'input[name="hotelwp_cf_field_terms_and_cond"]' ).prop( 'checked' ) )
		{
			policies_error = hotelwp_contact_form_text.terms_and_cond_error;
		}
		if (
			$form.find( 'input[name="hotelwp_cf_field_privacy_policy"]' ).length &&
			! $form.find( 'input[name="hotelwp_cf_field_privacy_policy"]' ).prop( 'checked' ) )
		{
			if ( policies_error ) {
				policies_error += '<br/>';
			}
			policies_error += hotelwp_contact_form_text.privacy_policy_error;
		}
		if ( policies_error ) {
			$form.find( '.hotelwp-cf-error' ).html( policies_error ).slideDown(
				400,
				function() {
					var page_padding_top = 0;
					if ( $( '.header' ).hasClass( 'is-fixed-header' ) ) {
						page_padding_top = $( '.header' ).height();
					}
					if ( $( '#wpadminbar' ).length ) {
						page_padding_top += $( '#wpadminbar' ).height();
					}
					$( 'layout, body' ).animate({
						scrollTop: $form.find( '.hotelwp-cf-policies-area' ).offset().top - page_padding_top
					}, 400 );
				}
			);
			return false;
		}
		if ( $form.hasClass( 'already-sent' ) ) {
			$form.find( '.hotelwp-cf-error' ).html( hotelwp_contact_form_text.contact_already_sent ).slideDown();
			return false;
		}
		if ( ! $form.hasClass( 'submitted' ) ) {
			disable_form_submission( $form );
			$form.find( '.hotelwp-cf-processing' ).fadeIn();
		} else {
			return false;
		}
		$.ajax({
			data: $form.serialize(),
			success: function( response ) {
				after_contact_form_submit( response, $form );
			},
			type: 'POST',
            timeout: 10000,
			url: hotelwp_contact_form_data.ajax_url,
			error: function( jqXHR, textStatus, errorThrown ) {
                $form.find( '.hotelwp-cf-processing' ).fadeOut();
				$form.find( '.hotelwp-cf-error' ).html( hotelwp_contact_form_text.connection_error ).slideDown();
				enable_form_submission( $form );
			}
		});
	}

	function after_contact_form_submit( response_text, $form ) {
		$form.find( '.hotelwp-cf-processing' ).fadeOut();
		enable_form_submission( $form );
		try {
			var response = JSON.parse( response_text );
		} catch ( e ) {
			$form.find( '.hotelwp-cf-error' ).html( response_text ).slideDown();
			return false;
		}
		if ( response['success'] ) {
			$form.find( '.hotelwp-cf-msg-send' ).html( response['msg'] ).slideDown();
			$form.addClass( 'already-sent' );
		} else {
			$form.find( '.hotelwp-cf-error' ).html( response['error_msg'] ).slideDown();
		}
	}

	$( '.hotelwp-contact-form input, .hotelwp-contact-form textarea' ).change( function() {
		$( this ).parents( '.hotelwp-contact-form' ).removeClass( 'already-sent' );
	});

	function disable_form_submission( $form ) {
		$form.addClass( 'submitted' );
		$form.find( 'input[type="submit"]' ).prop( 'disabled', true );
	}

	function enable_form_submission( $form ) {
		$form.removeClass( 'submitted' );
		$form.find( 'input[type="submit"]' ).prop( 'disabled', false );
	}

	function debouncer( func ) {
		var timeoutID,
			timeout = 50;
		return function () {
			var scope = this,
				args = arguments;
			clearTimeout( timeoutID );
			timeoutID = setTimeout( function () {
				func.apply( scope , Array.prototype.slice.call( args ) );
			} , timeout );
		}
	}

	$( window ).resize( debouncer( function () {
		$( '.hotelwp-contact-form' ).each( function() {
			if ( $( this ).width() < 600 ) {
				$( this ).addClass( 'hotelwp-cf-details-form-stacked' );
			} else {
				$( this ).removeClass( 'hotelwp-cf-details-form-stacked' );
			}
		});
	})).resize();

});