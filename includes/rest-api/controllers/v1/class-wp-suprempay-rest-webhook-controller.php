<?php
/**
 * REST API Webhook controller
 * Handles requests to the /webhook endpoint.
 *
 * @author  Mateus_Costa
 * @package WooCommerce_SupremPay/Classes/Rest-Api/Controllers/v1
 * @version 1.0.0
 */
defined( "ABSPATH" ) || exit;

/**
 * REST API Webhook controller class.
 */
class WP_SupremPay_Rest_Webhook_Controller extends WP_REST_Controller
{
	/**
	 * Endpoint namespace.
	 *
	 * @var 	string
	 * @since 	1.0.0
	 */
	protected $namespace = "suprempay/v1";

	/**
	 * Route base.
	 *
	 * @var 	string
	 * @since 	1.0.0
	 */
	protected $rest_base = "webhook";

	/**
	 * Stores the request.
	 *
	 * @var 	array
	 * @since 	1.0.0
	 */
	protected $request   = [];

	/**
	 * Logger
	 *
	 * @var 	WC_Logger
	 * @since 	1.0.0
	 */
	protected $log;

	/**
	 * Construct
	 *
	 * @since 	1.0.0
	 */
	public function __construct()
	{
		$this->log = ( function_exists( "wc_get_logger" ) ) ? wc_get_logger() : new WC_Logger();
	}

	/**
	 * Register the routes for users.
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function register_routes()
	{
		register_rest_route(
			$this->namespace,
			"/$this->rest_base/billet",
			[
				[
					"methods"             => WP_REST_Server::CREATABLE, // POST
                    "callback"            => [ $this, "billet" ],
                    "permission_callback" => "__return_true",
                ],
			]
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/pix",
			[
				[
					"methods"             => WP_REST_Server::CREATABLE, // POST
                    "callback"            => [ $this, "pix" ],
                    "permission_callback" => "__return_true",
                ],
			]
		);

		register_rest_route(
			$this->namespace,
			"/$this->rest_base/ted",
			[
				[
					"methods"             => WP_REST_Server::CREATABLE, // POST
                    "callback"            => [ $this, "ted" ],
                    "permission_callback" => "__return_true",
                ],
			]
		);
	}

	/**
	 * Webhook billet
	 *
	 * @since 	1.0.0
	 * @param 	WP_REST_Request $data
	 * @return 	WP_REST_Response
	 */
	public function billet( WP_REST_Request $request )
	{
		/**
		 * TODO tratar os dados
		 */
		$param    = $request->get_params();

		$data     = [
			"success" => true,
			"message" => "ok",
		];

		$response = new WP_REST_Response( $data, 200 );

		return $response;
	}

	/**
	 * Webhook pix
	 *
	 * @since 	1.0.0
	 * @param 	WP_REST_Request $data
	 * @return 	WP_REST_Response
	 */
	public function pix( WP_REST_Request $request )
	{
		/**
		 * TODO tratar os dados
		 */
		$param    = $request->get_params();

		$data     = [
			"success" => true,
			"message" => "ok",
		];

		$response = new WP_REST_Response( $data, 200 );

		return $response;
	}

	/**
	 * Webhook ted
	 *
	 * @since 	1.0.0
	 * @param 	WP_REST_Request $data
	 * @return 	WP_REST_Response
	 */
	public function ted( WP_REST_Request $request )
	{
		/**
		 * TODO tratar os dados
		 */
		$param    = $request->get_params();

		$data     = [
			"success" => true,
			"message" => "ok",
		];

		$response = new WP_REST_Response( $data, 200 );

		return $response;
	}

}
