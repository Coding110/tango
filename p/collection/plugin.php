<?php
/**
 * Plugin Name: Tango Collection
 * Description: Do thing for tango
 * Author: Becktu Team
 * Author URI: http://www.becktu.com/
 * Version: 1.0
 * Plugin URI: None 
 */

/**
 * Version number for our API
 *
 * @var string
 */
define( 'TANGO_VERSION', '1.0' );

//include_once( dirname( __FILE__ ) . '/tangohelper.php' );
/**
 * Register our rewrite rules for the API
 */
function tango_api_init() {
	tango_api_register_rewrites();
	//echo "<h5>tango_api_init</h5>";

	global $wp;
	$wp->add_query_var( 'tango_route' );
}
add_action( 'init', 'tango_api_init' );

function tango_api_register_rewrites() {
	//echo "<h5>tango_api_register_rewrites</h5>";
	add_rewrite_rule( '^' . tango_get_url_prefix() . '/?$','index.php?tango_route=/','top' );
	add_rewrite_rule( '^' . tango_get_url_prefix() . '(.*)?','index.php?tango_route=$matches[1]','top' );
}

/**
 * Get the URL prefix for any API resource.
 *
 * @return string Prefix.
 */
function tango_get_url_prefix() {
	//echo "<h5>tango_get_url_prefix</h5>";
	return apply_filters( 'tango_url_prefix', 'v' );
}

/**
 * Determine if the rewrite rules should be flushed.
 */
function tango_api_maybe_flush_rewrites() {
	//echo "<h5>tango_api_maybe_flush_rewrites</h5>";
    $version = get_option( 'tango_api_plugin_version', null );

    if ( empty( $version ) ||  $version !== DSPAY_API_VERSION ) {
        flush_rewrite_rules();
        update_option( 'tango_api_plugin_version', DSPAY_API_VERSION );
    }

}
add_action( 'init', 'tango_api_maybe_flush_rewrites', 999 );

/**
 *	Load the router
 *	e.g.: 
 *		1).$home_url/sh/7b646c8d
 *		2).$home_url/sh/alipay/return
 *		3).$home_url/sh/alipay/notify
 *		4).$home_url/sh/mng/{handle}
 *
 */
function tango_api_loaded() {
	if ( empty( $GLOBALS['wp']->query_vars['tango_route'] ) )
		return;

	$args = explode('/', $GLOBALS['wp']->query_vars['tango_route']);
	//var_dump($args);
	if(count($args) == 3){
		// simple video source submit and for chrome extention
		if($args[1] == "task"){ 
			if($args[1] == "task"){ // valid request
				if($args[2] == "submit"){ // submit video source URL
					$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					echo $url;
				}else if($args[2] == "update"){ // 
				}else if($args[2] == "check"){ // 
				}else if($args[2] == "status"){ //  mayb no need
				}
			}else{
				header("HTTP/1.0 404 Not Found");
				echo "Some error 2\n";
			}
		}else{
			header("HTTP/1.0 404 Not Found");
			echo "Some error 1\n";
		}
	}else if(count($args) == 4){
		// security video source submit 
		// visit video/audio/subtitle/thumbnail
	}else{
		// Error
		header("HTTP/1.0 404 Not Found");
		echo "Some error 0\n";
	}

	// Finish off our request
	die();
}
add_action( 'template_redirect', 'tango_api_loaded', -100 );

/**
 * Register routes and flush the rewrite rules on activation.
 */
function tango_api_activation( $network_wide ) {
	if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
		$mu_blogs = wp_get_sites();

		foreach ( $mu_blogs as $mu_blog ) {
			switch_to_blog( $mu_blog['blog_id'] );

			tango_api_register_rewrites();
			update_option( 'tango_api_plugin_version', null );
		}

		restore_current_blog();
	} else {
		tango_api_register_rewrites();
		update_option( 'tango_api_plugin_version', null );
	}

	//dash_tables_init();
}
register_activation_hook( __FILE__, 'tango_api_activation' );

/**
 * Flush the rewrite rules on deactivation
 */
function tango_api_deactivation( $network_wide ) {
	if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {

		$mu_blogs = wp_get_sites();

		foreach ( $mu_blogs as $mu_blog ) {
			switch_to_blog( $mu_blog['blog_id'] );
			delete_option( 'tango_api_plugin_version' );
		}

		restore_current_blog();
	} else {
		delete_option( 'tango_api_plugin_version' );
	}
}
register_deactivation_hook( __FILE__, 'tango_api_deactivation' );


/**
 * Check for errors when using cookie-based authentication
 *
 * WordPress' built-in cookie authentication is always active for logged in
 * users. However, the API has to check nonces for each request to ensure users
 * are not vulnerable to CSRF.
 *
 * @param WP_Error|mixed $result Error from another authentication handler, null if we should handle it, or another value if not
 * @return WP_Error|mixed|boolean
 */
function tango_cookie_check_errors( $result ) {
	if ( ! empty( $result ) ) {
		return $result;
	}

	global $wp_json_auth_cookie;

	// Are we using cookie authentication?
	// (If we get an auth error, but we're still logged in, another
	// authentication must have been used.)
	if ( $wp_json_auth_cookie !== true && is_user_logged_in() ) {
		return $result;
	}

	// Do we have a nonce?
	$nonce = null;
	if ( isset( $_REQUEST['_wp_json_nonce'] ) ) {
		$nonce = $_REQUEST['_wp_json_nonce'];
	} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
		$nonce = $_SERVER['HTTP_X_WP_NONCE'];
	}

	if ( $nonce === null ) {
		// No nonce at all, so act as if it's an unauthenticated request
		wp_set_current_user( 0 );
		return true;
	}

	// Check the nonce
	$result = wp_verify_nonce( $nonce, 'wp_json' );
	if ( ! $result ) {
		return new WP_Error( 'json_cookie_invalid_nonce', __( 'Cookie nonce is invalid' ), array( 'status' => 403 ) );
	}

	return true;
}
add_filter( 'json_authentication_errors', 'tango_cookie_check_errors', 100 );

/**
 * Collect cookie authentication status
 *
 * Collects errors from {@see wp_validate_auth_cookie} for use by
 * {@see tango_cookie_check_errors}.
 *
 * @param mixed
 */
function tango_cookie_collect_status() {
	global $wp_json_auth_cookie;

	$status_type = current_action();

	if ( $status_type !== 'auth_cookie_valid' ) {
		$wp_json_auth_cookie = substr( $status_type, 12 );
		return;
	}

	$wp_json_auth_cookie = true;
}
add_action( 'auth_cookie_malformed',    'tango_cookie_collect_status' );
add_action( 'auth_cookie_expired',      'tango_cookie_collect_status' );
add_action( 'auth_cookie_bad_username', 'tango_cookie_collect_status' );
add_action( 'auth_cookie_bad_hash',     'tango_cookie_collect_status' );
add_action( 'auth_cookie_valid',        'tango_cookie_collect_status' );

/*
 *	Change login logo
 */
function tango_login_logo() { 
	echo "<style type=\"text/css\">
        .login h1 a {
            background-image: url(/static/ds-logo-1.2-64.png);
            padding-bottom: 30px;
        }
    </style>";
}
add_action( 'login_enqueue_scripts', 'tango_login_logo' );

/*
 *	Modify phpmailer default settings
 */
add_action( 'phpmailer_init', 'smtp_mailer_init' );
function smtp_mailer_init ( $phpmailer ) {

	// Define that we are sending with SMTP
	//$phpmailer->isSMTP();

	// The hostname of the mail server
	//$phpmailer->Host = "smtp.qq.com";

	// Use SMTP authentication (true|false)
	//$phpmailer->SMTPAuth = true;

	// SMTP port number - likely to be 25, 465 or 587
	//$phpmailer->Port = "25";

	// Username to use for SMTP authentication
	//$phpmailer->Username = "becktu";

	// Password to use for SMTP authentication
	//$phpmailer->Password = "becktu123";

	// Encryption system to use - ssl or tls
	//$phpmailer->SMTPSecure = "tls";

	$phpmailer->From = "support@becktu.com";
	$phpmailer->FromName = "贝壳视频";
}

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function ds_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	global $user;
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			// redirect them to the default place
			return $redirect_to;
		} else {
			return home_url();
		}
	} else {
		return $redirect_to;
	}
}
add_filter( 'login_redirect', 'ds_login_redirect', 10, 3 );

