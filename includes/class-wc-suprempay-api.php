<?php
/**
 * API class
 *
 * @author  Mateus_Costa
 * @package WooCommerce_SupremPay/Classes/Api
 * @version 1.0.0
 */
defined( "ABSPATH" ) || exit;

class WC_SupremPay_API
{
	/**
	 * Endpoint homol.
	 *
	 * @var 	string
	 * @since 	1.0.0
	 */
	const API_HML    = "https://demoapi.suprem.cash";

	/**
	 * Endpoint prod.
	 *
	 * @var 	string
	 * @since 	1.0.0
	 */
	const API_PRD    = "https://api.suprem.cash";

	/**
	 * Http codes.
	 *
	 * @var 	array
	 * @since 	1.0.0
	 */
	const HTTP_CODES = [
		// [Informational 1xx]
		100 => "Continue",
		101 => "Switching Protocols",

		// [Successful 2xx]
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",

		// [Redirection 3xx]
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Found",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		306 => "(Unused)",
		307 => "Temporary Redirect",

		// [Client Error 4xx]
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Timeout",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Long",
		415 => "Unsupported Media Type",
		416 => "Requested Range Not Satisfiable",
		417 => "Expectation Failed",

		// [Server Error 5xx]
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Timeout",
		505 => "HTTP Version Not Supported",
	];

	/**
	 * Gateway class.
	 *
	 * @var 	WC_SupremPay_Gateway
	 * @since 	1.0.0
	 */
	protected $gateway;

	/**
	 * Constructor.
	 *
	 * @since 	1.0.0
	 * @param 	WC_SupremPay_Gateway $gateway Gateway instance.
	 */
	public function __construct( $gateway = null )
    {
		$this->gateway = $gateway;
		$this->register_weehook();
	}

	/**
	 * Return api url.
	 *
	 * @since 	1.0.0
	 * @return 	string
	 */
	private function get_api_url()
	{
		return ( $this->gateway->get_is_homol() === "yes" )
				? self::API_HML
				: self::API_PRD;
	}

	/**
	 * Exec the calls
	 *
	 * @since 	1.0.0
	 * @param 	string 	$api 			Api route to be call
	 * @param 	string 	$method 		Method of api route
	 * @param 	array 	$add_header 	Additional header to be sent in the header
	 * @param 	array 	$params 		Data to be sent in the body
	 * @return 	array
	 */
	private function exec( $api, $method, $add_header = [], $params = null )
    {
		$this->log( "INFO - running api : /{$api}" );

		$curl   = curl_init();

		if ( !$curl )
		{
			$result = [
				"code"    => "500.1",
				"success" => false,
				"message" => "Falha na comunicação com o meio de pagamento.",
				"error"   => "Couldn't initialize a cURL handle",
			];

			$this->log( json_encode( $result ), true );

			return $result;
		}

		$url    = $this->get_api_url() . "/{$api}";
		$tkn    = $this->gateway->get_token_integration();
		$header = [ "Content-Type: multipart/form-data", "token-integration: {$tkn}" ];

		if ( !empty( $add_header ) )
			foreach ( $add_header as $value )
				$header[] = $value;

		curl_setopt_array( $curl, [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_POSTFIELDS     => ( $params ) ? $params : null,
			CURLOPT_HTTPHEADER     => $header,
        ] );

        $resp = curl_exec( $curl );

		if ( empty( $resp ) )
		{
			// some kind of an error happened
			$err    = curl_error( $curl );
			curl_close($curl);

			$result = [
				"code"    => "500.2",
				"success" => false,
				"message" => "Falha na comunicação com o meio de pagamento.",
				"error"   => $err,
				"data"    => null,
			];

			$this->log( json_encode( $result ), true );

			return $result;
		}
		else
		{
			$info = curl_getinfo( $curl );

			curl_close( $curl );

			if ( empty( $info[ "http_code" ] ) )
			{
				$result = [
					"code"    => "500.2",
					"success" => false,
					"message" => "Falha na comunicação com o meio de pagamento.",
					"error"   => "No HTTP code was returned.",
					"data"    => json_decode( $resp, true ),
				];

				$this->log( json_encode( $result ), true );

				return $result;
			}
			else if ( $info[ "http_code" ] < 200 || $info[ "http_code" ] >= 300 )
			{
				$result = [
					"code"    => $info[ "http_code" ],
					"success" => false,
					"message" => "Falha na comunicação com o meio de pagamento.",
					"error"   => self::HTTP_CODES[ $info[ "http_code" ] ],
					"data"    => json_decode( $resp, true ),
				];

				$this->log( json_encode( $result ), true );

				return $result;
			}
			else
			{
				$result = [
					"code"    => $info[ "http_code" ],
					"success" => true,
					"message" => "ok",
					"error"   => null,
					"data"    => json_decode( $resp, true ),
				];

				$this->log( json_encode( $result ), true );

				return $result;
			}
		}
    }

	/**
	 * Process the payment
	 *
	 * @since 	1.0.0
	 * @param 	string 	$method 	Payment method (CC, Pix, Ticket)
	 * @param 	array 	$data 		Data of the payment
	 * @return 	array
	 */
	public function do_payment( $method, $data )
	{
		switch ( $method )
		{
			case "pix":
				return $this->do_payment_pix( $data );
			break;
		}
	}

	/**
	 * Process credit card payment
	 *
	 * @since 	1.0.0
	 * @param 	array 	$data 	Data of the payment
	 * @return 	array
	 */
	private function do_payment_cc( $data )
	{
		/**
		 * $tokens = [
		 * 	"payments" => [
		 * 		"order_id_1" => "payment_id_1",
		 * 		"order_id_2" => "payment_id_2",
		 * 	],
		 * 	"cards"    => [
		 * 		"brand_1" => [
		 * 			[
		 * 				"digits"    => "1234",
		 * 				"cvc"       => "123",
		 * 				"card_code" => "token_gerado_anteriormente",
		 * 			], [
		 * 				"digits"    => "4321",
		 * 				"cvc"       => "321",
		 * 				"card_code" => "token_gerado_anteriormente",
		 * 			]
		 * 		]
		 * 	]
		 * ];
		 */

		$user         = $data[ "user" ];
		$tokens       = get_user_meta( $user->ID, "_suprempay_tokens", true );

		$empty        = empty( $tokens );
		$notSetBrand  = !$empty && isset( $tokens[ "cards" ] ) && !isset( $tokens[ "cards" ][ $data[ "brand" ] ] );

		$needNewToken = false;

		if ( $empty || $notSetBrand )
		{
			$needNewToken = true;
		}
		else
		{
			// retrieve token
			$lastDigits = substr( $data[ "card_number" ], -4 );
			$cards      = $tokens[ "cards" ][ $data[ "brand" ] ];
			$key        = array_search( $lastDigits, array_column( $cards, "digits" ) );

			if ( $key === false )
			{
				$needNewToken = true;
			}
			else
			{
				$card = $cards[ $key ];

				if ( $card[ "cvc" ] != $data[ "cvc" ] ) $needNewToken = true;
			}
		}

		// generate token
		if ( $needNewToken )
		{
			$response = $this->create_card_token( [
				"holder"      => $data[ "holder"      ],
				"card_number" => $data[ "card_number" ],
				"brand"       => $data[ "brand"       ],
				"expiration"  => $data[ "month" ] . "/" . $data[ "year" ]
			] );

			if ( $response[ "success" ] )
			{
				$card = [
					"digits"    => substr( $data[ "card_number" ], -4 ),
					"cvc"       => $data[ "cvc" ],
					"card_code" => $response[ "data" ][ "cardCode" ],
				];

				if ( empty( $tokens ) )
				{
					$tokens = [ "cards" => [ $data[ "brand" ] => [ $card ] ] ];
				}
				else
				{
					$tokens[ "cards" ][ $data[ "brand" ] ][] = $card;
				}

				update_user_meta( $user->ID, "_suprempay_tokens", $tokens );
			}
			else
			{
				return $response;
			}
		}

		$product  = ( $data[ "installment" ] == 1 && $this->gateway->get_cc_type() != "avista" ) ? "avista" : $this->gateway->get_cc_type();
		$header   = [ "ProductType: {$product}" ];

		$prefix   = $this->gateway->get_invoice_prefix();
		$orderId  = "#{$prefix}{$data[ "order_id" ]}";

		$value    = ( float ) $data[ "total" ];

		$params   = [
			"capture"                   => true,
			"orderNumber"               => $orderId,
			"installment"               => ( int ) $data[ "installment" ],
			"value"                     => $value, // reais
			"cardCode"                  => $card[ "card_code"  ],
			"SecurityCode"              => $card[ "cvc"        ],
			"customerFirstName"         => $data[ "first_name" ],
			"customerLastName"          => $data[ "last_name"  ],
			"customerDocument"          => $data[ "cpf"        ],
			"customerEmail"             => $data[ "email"      ],
			"customerPhoneNumber"       => $data[ "phone"      ],
			"customerAddress"           => $data[ "street"     ],
			"customerAddressComplement" => $data[ "complement" ],
			"customerState"             => $data[ "uf"         ],
			"customerZipCode"           => $data[ "zipcode"    ],
		];

		$response = $this->exec( "card", "POST", $header, $params );

		if ( $response[ "success" ] )
		{
			$tokens[ "payments" ][ $data[ "order_id" ] ] = $response[ "data" ][ "paymentId" ];

			update_user_meta( $user->ID, "_suprempay_tokens", $tokens );
		}

		return $response;
	}

	/**
	 * Process pix payment
	 *
	 * @since 	1.0.0
	 * @param 	array 	$data 	Data of the payment
	 * @return 	array
	 */
	private function do_payment_pix( $data )
	{
		$pixDate  = ( int ) $this->gateway->get_pix_due_date();

		$prefix   = $this->gateway->get_invoice_prefix();
		$order    = $data[ "order" ];
		$descrip  = "#{$prefix}{$order->get_id()} | {$data["name"]}";

		$amount   = ( float ) $data[ "amount" ];
		$email    = $data[ "email" ];

		$params   = [
			"token_api"   => $this->gateway->get_token_user(),
			"description" => substr( $descrip, 0, 150 ),
			"expiration"  => $pixDate,
			"amount"      => $amount,
			"email"       => $email,
		];

		$response = $this->exec( "pix/collection/wallet", "POST", [], $params );

		if ( $response[ "success" ] && isset( $response[ "data" ] ) && $response[ "data" ][ "success" ] )
		{
			$customer_id = $order->get_customer_id();
			$tokens      = get_user_meta( $customer_id, "_suprempay_tokens", true );
			$info        = $response[ "data" ][ "info" ];

			if ( empty( $tokens ) )
			{
				$tokens = [
					"payments" => [
						$order->get_id() => [
							"type"                => "pix",
							"payment_id"          => $info[ "pix_path"            ],
							"transaction_id"      => $info[ "transaction_id"      ],
							"guid_user"           => $info[ "guid_user"           ],
							"guid_pix_collection" => $info[ "guid_pix_collection" ],
						]
					],
				];
			}
			else
			{
				$tokens[ "payments" ][ $order->get_id() ] = [
					"type"                => "pix",
					"payment_id"          => $info[ "pix_path"            ],
					"transaction_id"      => $info[ "transaction_id"      ],
					"guid_user"           => $info[ "guid_user"           ],
					"guid_pix_collection" => $info[ "guid_pix_collection" ],
				];
			}

			update_user_meta( $customer_id, "_suprempay_tokens", $tokens );
		}

		return $response;
	}

	/**
	 * Process ticket payment
	 *
	 * @since 	1.0.0
	 * @param 	array 	$data 	Data of the payment
	 * @return 	array
	 */
	private function do_payment_ticket( $data )
	{
		$header   = [];

		$tic_date = $this->gateway->get_ticket_due_date();
		$due_date = date( "Y-m-d" ) . " +{$tic_date} days";
		$due_date = date( "Y-m-d", strtotime( $due_date ) );

		$prefix   = $this->gateway->get_invoice_prefix();
		$orderId  = "#{$prefix}{$data[ "order_id" ]}";
		$descript = "Pagamento do pedido {$orderId}";

		$zipcode  = preg_replace( "/\D/", "", $data[ "zipcode" ] );

		$value    = ( float ) $data[ "total" ];

		$params   = [
			"value"       => $value, // reais
			"dueDate"     => $due_date,
			"orderNumber" => $orderId,
			"description" => $descript,
			"installment" => 1,
			"toPerson"    => [
				"name"         => $data[ "name"         ],
				"document"     => $data[ "cpf"          ],
				"street"       => $data[ "street"       ],
				"neighborhood" => $data[ "neighborhood" ],
				"city"         => $data[ "city"         ],
				"uf"           => $data[ "uf"           ],
				"email"        => $data[ "email"        ],
				"ddd"          => $data[ "ddd"          ],
				"phone"        => $data[ "phone"        ],
				"zipcode"      => $zipcode,
			]
		];

		$response = $this->exec( "invoice", "POST", $header, $params );

		if ( $response[ "success" ] )
		{
			$user   = $data[ "user" ];
			$tokens = get_user_meta( $user->ID, "_suprempay_tokens", true );

			$response[ "data" ] = ( isset( $response[ "data" ][ 0 ] ) ) ? $response[ "data" ][ 0 ] : $response[ "data" ];

			if ( empty( $tokens ) )
			{
				$tokens = [ "payments" => [ $data[ "order_id" ] => $response[ "data" ][ "paymentId" ] ] ];
			}
			else
			{
				$tokens[ "payments" ][ $data[ "order_id" ] ] = $response[ "data" ][ "paymentId" ];
			}

			update_user_meta( $user->ID, "_suprempay_tokens", $tokens );
		}

		return $response;
	}

	/**
	 * Register webhooks
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	private function register_weehook()
	{
		if ( defined( "SUPREMPAY_WH_BIL" ) && defined( "SUPREMPAY_WH_PIX" ) && defined( "SUPREMPAY_WH_TED" ) ) return;

        $v         = WC_SUPREMPAY_REST_API_VER;
		$endpoints = [
			"BIL" => [
				"token_api" => $this->gateway->get_token_user(),
				"service"   => "collection-billet",
				"url"       => get_rest_url( null, "suprempay/{$v}/webhook/billet" ),
			],
			"PIX" => [
				"token_api" => $this->gateway->get_token_user(),
				"service"   => "collection-pix",
				"url"       => get_rest_url( null, "suprempay/{$v}/webhook/pix"    ),
			],
			"TED" => [
				"token_api" => $this->gateway->get_token_user(),
				"service"   => "collection-ted",
				"url"       => get_rest_url( null, "suprempay/{$v}/webhook/ted"    ),
			],
		];

		foreach ( $endpoints as $key => $endpoint )
		{
			$this->log( json_encode( $endpoint ) );

			$response = $this->exec( "webhook/set", "POST", [], $endpoint );

			if ( $response[ "success" ] && isset( $response[ "data" ] ) && $response[ "data" ][ "success" ] )
				define( "SUPREMPAY_WH_{$key}", true );

			/**
			 * TODO remover na finalização do plugin
			 */
			if ( $response[ "success" ] && isset( $response[ "data" ] ) && $response[ "data" ][ "message" ] == "Please enter a valid url!" )
				define( "SUPREMPAY_WH_{$key}", true );
		}
	}

	/**
	 * Log
	 *
	 * @since 	1.0.0
	 * @param 	string 		$message
	 * @param 	boolean 	$is_error
	 * @return 	void
	 */
	private function log( $message, $is_error = false )
	{
		$log = $this->gateway->get_log();

		if ( $this->gateway->get_debug() === "yes" )
		{
			$log->add( $this->gateway->id, $message );
		}
		else if ( $is_error )
		{
			$log->add( $this->gateway->id, $message );
		}
	}
}
