( function ( $ ) {
	"use strict";

	$( function () {

		$( document ).ready( function() {
			var open  = "open";
			var close = "close";

			$( "<button class='close show-hide' style='line-height:24px;' type='button'>Ver</button>" ).insertAfter( $( "#woocommerce_suprempay_token_user_hml"   ) );
			$( "<button class='close show-hide' style='line-height:24px;' type='button'>Ver</button>" ).insertAfter( $( "#woocommerce_suprempay_token_user"       ) );
			$( "<button class='close show-hide' style='line-height:24px;' type='button'>Ver</button>" ).insertAfter( $( "#woocommerce_suprempay_token_integr_hml" ) );
			$( "<button class='close show-hide' style='line-height:24px;' type='button'>Ver</button>" ).insertAfter( $( "#woocommerce_suprempay_token_integr"     ) );

			$( ".show-hide" ).on( "click", function() {
				if ( $( this ).hasClass( close ) )
				{
					$( this ).prev( "input[type='password']" ).prop( "type", "text"     );
					$( this ).removeClass( close );
					$( this ).addClass( open );
				}
				else
				{
					$( this ).prev( "input[type='text']"     ).prop( "type", "password" );
					$( this ).removeClass( open );
					$( this ).addClass( close );
				}
			} );
		} );

		// hml-prd
		function suprempaySwitchUserData( checked )
		{
			let token_user_hml   = $( "#woocommerce_suprempay_token_user_hml"   ).closest( "tr" ),
				token_user       = $( "#woocommerce_suprempay_token_user"       ).closest( "tr" ),
				token_integr_hml = $( "#woocommerce_suprempay_token_integr_hml" ).closest( "tr" ),
				token_integr     = $( "#woocommerce_suprempay_token_integr"     ).closest( "tr" );

			if ( checked )
			{
				token_user_hml.show();
				token_user.hide();
				token_integr_hml.show();
				token_integr.hide();
			}
			else
			{
				token_user_hml.hide();
				token_user.show();
				token_integr_hml.hide();
				token_integr.show();
			}
		}

		// // card
		// function suprempaySwitchCCData( checked )
		// {
		// 	let cc_type     = $( "#woocommerce_suprempay_cc_type"     ),
		// 		installment = $( "#woocommerce_suprempay_installment" ).closest( "tr" );

		// 	if ( checked )
		// 	{
		// 		cc_type.closest( "tr" ).show();

		// 		if ( cc_type.val() == "avista" )
		// 			installment.hide();
		// 		else
		// 			installment.show();
		// 	}
		// 	else
		// 	{
		// 		cc_type.closest( "tr" ).hide();
		// 		installment.hide();
		// 	}
		// }
		// function suprempaySwitchTypeCC( value )
		// {
		// 	let installment = $( "#woocommerce_suprempay_installment" ).closest( "tr" );

		// 	if ( value == "avista" )
		// 		installment.hide();
		// 	else
		// 		installment.show();
		// }

		// // pix / ticket
		// function suprempaySwitchDueDate( checked, field )
		// {
		// 	let due_date = $( `#woocommerce_suprempay_${field}_due_date` ).closest( "tr" );

		// 	if ( checked )
		// 		due_date.show();
		// 	else
		// 		due_date.hide();
		// }
		// // pix
		// function suprempaySwitchPixData( checked )
		// {
		// 	let whatsapp = $( "#woocommerce_suprempay_pix_whatsapp" ).closest( "tr" ),
		// 		telegram = $( "#woocommerce_suprempay_pix_telegram" ).closest( "tr" ),
		// 		email    = $( "#woocommerce_suprempay_pix_email"    ).closest( "tr" );

		// 	if ( checked )
		// 	{
		// 		whatsapp.show();
		// 		telegram.show();
		// 		email.show();
		// 	}
		// 	else
		// 	{
		// 		whatsapp.hide();
		// 		telegram.hide();
		// 		email.hide();
		// 	}
		// }


		// hml/prd
		suprempaySwitchUserData( $( "#woocommerce_suprempay_is_homol" ).is( ":checked" ) );

		// // card
		// suprempaySwitchCCData( $( "#woocommerce_suprempay_cc" ).is( ":checked" ) );
		// suprempaySwitchTypeCC( $( "#woocommerce_suprempay_cc_type" ).val() );

		// // pix
		// suprempaySwitchDueDate( $( "#woocommerce_suprempay_pix" ).is( ":checked" ), "pix" );
		// suprempaySwitchPixData( $( "#woocommerce_suprempay_pix" ).is( ":checked" )        );

		// // ticket
		// suprempaySwitchDueDate( $( "#woocommerce_suprempay_ticket" ).is( ":checked" ), "ticket" );


		$( "body" ).on( "change", "#woocommerce_suprempay_is_homol", function () {
			suprempaySwitchUserData( $( this ).is( ":checked" ) );
		});

		// // card
		// $( "body" ).on( "change", "#woocommerce_suprempay_cc", function () {
		// 	suprempaySwitchCCData( $( this ).is( ":checked" ) );
		// });
		// $( "body" ).on( "change", "#woocommerce_suprempay_cc_type", function () {
		// 	suprempaySwitchTypeCC( $( this ).val() );
		// });

		// // pix
		// $( "body" ).on( "change", "#woocommerce_suprempay_pix", function () {
		// 	suprempaySwitchDueDate( $( this ).is( ":checked" ), "pix" );
		// 	suprempaySwitchPixData( $( this ).is( ":checked" ),       );
		// });
		// // ticket
		// $( "body" ).on( "change", "#woocommerce_suprempay_ticket", function () {
		// 	suprempaySwitchDueDate( $( this ).is( ":checked" ), "ticket" );
		// });


		window.addEventListener( "load", function() {
			$( "#sp_tbu_value" ).maskMoney( {
				prefix: "R$ ",
				decimal: ",",
				thousands: ".",
				precision: 2,
				allowZero: false,
				allowNegative: false,
			} );

			$( "#suprempay-tbu" ).on( "submit", function( e ) {
				e.preventDefault();

				const p = "<b>SupremPay</b>: ";

				$( "#sp-tbu-btn" ).prop( "disabled", "disabled" ).find( "div" ).removeClass( "sp-d-none" );

				$( "#sp-notice-success" ).addClass( "sp-d-none" ).find( "p" ).html( "" );
				$( "#sp-notice-warning" ).addClass( "sp-d-none" ).find( "p" ).html( "" );
				$( "#sp-notice-error"   ).addClass( "sp-d-none" ).find( "p" ).html( "" );

				let url   = spas.ajaxurl;
				let email = $( "#sp_tbu_email" );
				let pinCd = $( "#sp_tbu_pin"   );
				let value = $( "#sp_tbu_value" );
				let nonce = $( "#sp_tbu_nonce" );

				if ( !email.val() || !pinCd.val() || !value.val() || !nonce.val() )
				{
					$( "#sp-notice-warning" ).removeClass( "sp-d-none" ).find( "p" ).html( p + "Todos os campos devem ser preenchidos!" );
					return;
				}

				$.ajax( {
					url,
					type : "POST",
					data : {
						action : "suprempay_tbu",
						email  : email.val(),
						pin    : pinCd.val(),
						value  : value.val(),
						nonce  : nonce.val(),
					},
					success: function( data ) {
						console.log( data );

						let result = JSON.parse( data );
						console.log( result );

						switch ( result.code ) {
							case 200:
								$( "#sp-notice-success" ).removeClass( "sp-d-none" ).find( "p" ).html( p + "Transferência realizada com sucesso!" );
								email.val( "" );
								pinCd.val( "" );
								value.val( "" );
								nonce.val( "" );
							break;

							case 400:
							case 404:
								$( "#sp-notice-error"   ).removeClass( "sp-d-none" ).find( "p" ).html( p + result.message );
							break;

							default:
								$( "#sp-notice-warning" ).removeClass( "sp-d-none" ).find( "p" ).html( p + "Algo deu errado!" );
							break;
						}

						jQuery( "#sp-tbu-btn" ).prop( "disabled", null ).find( "div" ).addClass( "sp-d-none" );
					},
					error: function( err ) {
						console.log( err );

						$( "#sp-notice-error" ).removeClass( "sp-d-none" ).find( "p" ).html( p + "Ocorreu um erro!" );
						jQuery( "#sp-tbu-btn" ).prop( "disabled", null ).find( "div" ).addClass( "sp-d-none" );
					}
				} );
			} );

		} );
	} );

}( jQuery ) );
