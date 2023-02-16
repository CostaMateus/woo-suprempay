(function ( $ ) {
	"use strict";

	$( function () {

		// hml-prd
		function suprempaySwitchUserData( checked )
		{
			let access_key_hml = $( "#woocommerce_suprempay_access_key_hml" ).closest( "tr" ),
				access_key     = $( "#woocommerce_suprempay_access_key"     ).closest( "tr" );

			if ( checked )
			{
				access_key_hml.show();
				access_key.hide();
			}
			else
			{
				access_key_hml.hide();
				access_key.show();
			}
		}

		// card
		function suprempaySwitchCCData( checked )
		{
			let cc_type     = $( "#woocommerce_suprempay_cc_type"     ),
				installment = $( "#woocommerce_suprempay_installment" ).closest( "tr" );

			if ( checked )
			{
				cc_type.closest( "tr" ).show();

				if ( cc_type.val() == "avista" )
					installment.hide();
				else
					installment.show();
			}
			else
			{
				cc_type.closest( "tr" ).hide();
				installment.hide();
			}
		}
		function suprempaySwitchTypeCC( value )
		{
			let installment = $( "#woocommerce_suprempay_installment" ).closest( "tr" );

			if ( value == "avista" )
				installment.hide();
			else
				installment.show();
		}

		// pix / ticket
		function suprempaySwitchDueDate( checked, field )
		{
			let due_date = $( `#woocommerce_suprempay_${field}_due_date` ).closest( "tr" );

			if ( checked )
				due_date.show();
			else
				due_date.hide();
		}
		// pix
		function suprempaySwitchPixData( checked )
		{
			let whatsapp = $( "#woocommerce_suprempay_pix_whatsapp" ).closest( "tr" ),
				telegram = $( "#woocommerce_suprempay_pix_telegram" ).closest( "tr" ),
				email    = $( "#woocommerce_suprempay_pix_email"    ).closest( "tr" );

			if ( checked )
			{
				whatsapp.show();
				telegram.show();
				email.show();
			}
			else
			{
				whatsapp.hide();
				telegram.hide();
				email.hide();
			}
		}


		// hml/prd
		suprempaySwitchUserData( $( "#woocommerce_suprempay_is_homol" ).is( ":checked" ) );

		// card
		suprempaySwitchCCData( $( "#woocommerce_suprempay_cc" ).is( ":checked" ) );
		suprempaySwitchTypeCC( $( "#woocommerce_suprempay_cc_type" ).val() );

		// pix
		suprempaySwitchDueDate( $( "#woocommerce_suprempay_pix" ).is( ":checked" ), "pix" );
		suprempaySwitchPixData( $( "#woocommerce_suprempay_pix" ).is( ":checked" )        );

		// ticket
		suprempaySwitchDueDate( $( "#woocommerce_suprempay_ticket" ).is( ":checked" ), "ticket" );


		$( "body" ).on( "change", "#woocommerce_suprempay_is_homol", function () {
			suprempaySwitchUserData( $( this ).is( ":checked" ) );
		});

		// card
		$( "body" ).on( "change", "#woocommerce_suprempay_cc", function () {
			suprempaySwitchCCData( $( this ).is( ":checked" ) );
		});
		$( "body" ).on( "change", "#woocommerce_suprempay_cc_type", function () {
			suprempaySwitchTypeCC( $( this ).val() );
		});

		// pix
		$( "body" ).on( "change", "#woocommerce_suprempay_pix", function () {
			suprempaySwitchDueDate( $( this ).is( ":checked" ), "pix" );
			suprempaySwitchPixData( $( this ).is( ":checked" ),       );
		});
		// ticket
		$( "body" ).on( "change", "#woocommerce_suprempay_ticket", function () {
			suprempaySwitchDueDate( $( this ).is( ":checked" ), "ticket" );
		});
	});

}( jQuery ));
