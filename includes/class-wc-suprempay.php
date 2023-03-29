<?php
/**
 * Plugin's main class
 *
 * @author  Mateus_Costa
 * @package WooCommerce_SupremPay
 * @version 1.0.0
 */
defined( "ABSPATH" ) || exit;

/**
 * WC_SupremPay bootstrap class.
 */
class WC_SupremPay
{
	/**
	 * Instance of this class.
	 *
	 * @since 	1.0.0
	 * @var 	object
	 */
	protected static $instance = null;

	/**
	 * Construct
	 *
	 * @since 	1.0.0
	 */
	private function __construct()
    {
		if ( class_exists( "WooCommerce" ) )
		{
			if ( class_exists( "Extra_Checkout_Fields_For_Brazil" ) )
			{
				include_once dirname( __FILE__ ) . "/class-wc-suprempay-api.php";
				include_once dirname( __FILE__ ) . "/class-wc-suprempay-gateway.php";
				include_once dirname( __FILE__ ) . "/rest-api/class-wp-suprempay-rest-api.php";

				new WP_SupremPay_Rest_Api();

				add_filter( "woocommerce_payment_gateways",           [ $this, "add_gateway"                  ]       );
				add_filter( "woocommerce_available_payment_gateways", [ $this, "hides_when_is_outside_brazil" ]       );
				add_filter( "woocommerce_billing_fields",             [ $this, "checkout_billing_fields"      ], 9999 );
				add_filter( "woocommerce_shipping_fields",            [ $this, "checkout_shipping_fields"     ], 9999 );

				add_filter( "plugin_action_links_" . plugin_basename( WC_SUPREMPAY_PLUGIN_FILE ), [ $this, "plugin_action_links" ] );

				add_action( "admin_menu",                   [ $this, "add_admin_menu"         ], 9 );
				add_action( "admin_head",                   [ $this, "admin_css_js"           ]    );

				add_action( "wp_ajax_suprempay_tbu",        [ $this, "transfer_between_users" ]    );
				add_action( "wp_ajax_nopriv_suprempay_tbu", [ $this, "transfer_between_users" ]    );
			}
			else
			{
				add_action( "admin_notices", [ $this, "ecfb_missing_notice" ] );
			}
		}
		else
		{
			add_action( "admin_notices", [ $this, "woocommerce_missing_notice" ] );
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 	1.0.0
	 * @return 	object 	A single instance of this class.
	 */
	public static function init()
    {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Get templates path.
	 *
	 * @since 	1.0.0
	 * @return 	string
	 */
	public static function get_templates_path()
    {
		return plugin_dir_path( WC_SUPREMPAY_PLUGIN_FILE ) . "templates/";
	}

	/**
	 * Action links.
	 *
	 * @since 	1.0.0
	 * @param 	array $links
	 * @return 	array
	 */
	public function plugin_action_links( $links )
    {
        $url            = esc_url( admin_url( "admin.php?page=wc-settings&tab=checkout&section=suprempay" ) );
        $text           = __( "Configurações", WC_SUPREMPAY_DOMAIN );
		$plugin_links   = [];
		$plugin_links[] = "<a href='{$url}' >{$text}</a>";

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @since 	1.0.0
	 * @param 	array $methods 	WooCommerce payment methods.
	 * @return 	array 			Payment methods with SupremPay.
	 */
	public function add_gateway( $methods )
    {
		$methods[] = "WC_SupremPay_Gateway";

		return $methods;
	}

	/**
	 * Hides the SupremPay with payment method with the customer lives outside Brazil.
	 *
	 * @since 	1.0.0
	 * @param 	array $available_gateways 	Default Available Gateways.
	 * @return 	array 						New Available Gateways.
	 */
	public function hides_when_is_outside_brazil( $available_gateways )
    {
		// Remove SupremPay gateway.
		if ( isset( $_REQUEST[ "country" ] ) && $_REQUEST[ "country" ] !== "BR" )
			unset( $available_gateways[ "suprempay" ] );

		return $available_gateways;
	}

	/**
	 * Checkout billing fields.
	 *
	 * @since 	1.0.0
	 * @param 	array $fields
	 * @return 	array
	 */
	public function checkout_billing_fields( $fields )
	{
		if ( isset( $fields[ "billing_neighborhood" ] ) )
			$fields[ "billing_neighborhood" ][ "required" ] = true;

		if ( isset( $fields[ "billing_number" ] ) )
			$fields[ "billing_number"       ][ "required" ] = true;

		return $fields;
	}

	/**
	 * Checkout shipping fields.
	 *
	 * @since 	1.0.0
	 * @param 	array $fields
	 * @return 	array
	 */
	public function checkout_shipping_fields( $fields )
	{
		if ( isset( $fields[ "shipping_neighborhood" ] ) )
			$fields[ "shipping_neighborhood" ][ "required" ] = true;

		if ( isset( $fields[ "shipping_number" ] ) )
			$fields[ "shipping_number"       ][ "required" ] = true;

		return $fields;
	}

	/**
	 * WooCommerce Extra Checkout Fields for Brazil notice.
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function ecfb_missing_notice()
    {
		include dirname( __FILE__ ) . "/admin/views/html-notice-missing-ecfb.php";
	}

	/**
	 * WooCommerce missing notice.
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function woocommerce_missing_notice()
    {
		include dirname( __FILE__ ) . "/admin/views/html-notice-missing-woocommerce.php";
	}

	/**
	 * Add submenu on WP Settings menu
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function add_admin_menu()
	{
		add_menu_page(
			"SupremPay",
			"SupremPay",
			"manage_options",
			WC_SUPREMPAY_DOMAIN,
			[ $this, "admin_menu_settings" ],
			plugins_url( "assets/images/suprempay-white-icon-20.png", WC_SUPREMPAY_PLUGIN_FILE ),
            65
		);
	}

	/**
	 * Undocumented function
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function admin_menu_settings()
	{
		if ( !current_user_can( "manage_options" ) ) wp_die( __( "Você não tem permissões suficientes para acessar esta página." ) );

		if ( isset( $_GET[ "error_message" ] ) )
		{
			add_action( "admin_notices", [ $this, "settings_messages" ] );
			do_action( "admin_notices", $_GET[ "error_message" ] );
		}

		require_once ( plugin_dir_path( dirname( __FILE__ ) ) . "templates/admin/admin-settings.php" );
	}

	/**
	 * Handles errors and displays messages accordingly
	 *
	 * @since 	1.0.0
	 * @param 	string $error_message
	 */
	public function settings_messages( $error_message )
	{
		switch ( $error_message )
		{
			case "1":
				$message       = __( "There was an error adding this setting. Please try again. If this persists, shoot us an email.", WC_SUPREMPAY_DOMAIN );
				$err_code      = esc_attr(  "triibo_api_services-example-setting" );
				$setting_field = "triibo_api_services_example_setting";
			break;
		}

		$type = "error";

		add_settings_error( $setting_field, $err_code, $message, $type );
	}

	/**
	 * Undocumented function
	 *
	 * @since 	1.0.0
	 * @return 	void
	 */
	public function admin_css_js()
	{
		$suffix   = ""; // defined( "SCRIPT_DEBUG" ) && SCRIPT_DEBUG ? "" : ".min";

		$css_path = "assets/css/admin/style{$suffix}.css";
		$js_path  = "assets/js/admin/script{$suffix}.js";
		$mk_path  = "assets/js/admin/jquery.maskMoney.min.js";

		$css_ver  = date( "ymd-His", filemtime( plugin_dir_path( WC_SUPREMPAY_PLUGIN_FILE ) . $css_path ) );
		$js_ver   = date( "ymd-His", filemtime( plugin_dir_path( WC_SUPREMPAY_PLUGIN_FILE ) . $js_path  ) );
		$mk_ver   = date( "ymd-His", filemtime( plugin_dir_path( WC_SUPREMPAY_PLUGIN_FILE ) . $mk_path  ) );

		wp_enqueue_style( "sp_admin_css", plugins_url( $css_path, WC_SUPREMPAY_PLUGIN_FILE ), false, $css_ver, "all" );
		wp_enqueue_script( "jquery-maskMoney", plugins_url( $mk_path, WC_SUPREMPAY_PLUGIN_FILE ), [ "jquery" ], $mk_ver, true );
		wp_enqueue_script( "sp_admin_script", plugins_url( $js_path, WC_SUPREMPAY_PLUGIN_FILE ), [ "jquery", "jquery-maskMoney" ], $js_ver, true );

		wp_localize_script( "sp_admin_script", "spas", [
			"id"      => WC_SUPREMPAY_ID,
			"ajaxurl" => admin_url( "admin-ajax.php" ),
		] );
	}

	public function transfer_between_users()
	{
		if ( !isset( $_POST[ "nonce" ] ) || !wp_verify_nonce( $_POST[ "nonce" ], "sp_tbu_nonce" ) )
		{
			echo json_encode( [
				"code"    => 401,
				"message" => "Unauthorized",
			] );
			die();
		}

		$gateway  = WC_SupremPay_Gateway::init();
		$data     = $gateway->process_data_transfer( $_POST );
		$api      = $gateway->get_api();

		$response = $api->do_payment( "transfer", $data );

		if ( !$response[ "success" ] )
		{
			if ( $response[ "message" ] == "Sorry, we couldn't find this user!" )
			{
				$response[ "message" ] = "Carteira não encontrada";
				$response[ "code"    ] = 404;
			}
			else
			{
				$response[ "code"    ] = 400;
			}
		}
		else
		{
			$response[ "code" ] = 200;
		}

		$response = json_encode( $response );

		$api->log( $response );

		echo $response;
		die();
	}
}
