<?php

/**
 * Fired during plugin activation
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    OwnerRez
 * @subpackage OwnerRez/includes
 * @author     Dev <dev@ownerreservations.com>
 */
class OwnerRez_Api
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $ownerrez    The ID of this plugin.
	 */
	private $ownerrez;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $username;
	private $token;
	private $apiRoot;

	public function __construct($ownerrez, $version, $username, $token)
	{

		$this->ownerrez = $ownerrez;
		$this->version = $version;
		$this->username = $username;
		$this->token = $token;

		$this->apiRoot = "http://secure.dev.ownerreservations.com/api";
	}

	public function test_credentials($username, $token)
	{

		$headers = array("Authorization" => "Basic " . base64_encode($username . ":" . $token));

		$response = $this->call("/users/me", NULL, $headers);

		if (is_wp_error($response))
			return false;

		$body = json_decode(wp_remote_retrieve_body($response));

		return strcasecmp($body->EmailAddress, $username);
	}

	function call($path, $additionalArgs, $additionalHeaders)
	{

		$args = array(
			"method" => "GET",
			"headers" => array(
				"Authorization" => "Basic " . base64_encode($this->username . ":" . $this->token),
				"Content-Type" => "application/json",
				"User-Agent" => "wordpress-plugin-ownerrez-v" . $this->version
			)
		);

		if (isset($additionalArgs)) {
			foreach ($additionalArgs as $key => $value) {
				$args[$key] = $value;
			}
		}

		if (isset($additionalHeaders)) {
			foreach ($additionalHeaders as $key => $value) {
				$args["headers"][$key] = $value;
			}
		}

		$response       = wp_remote_request($this->apiRoot . $path, $args);
		$code           = wp_remote_retrieve_response_code($response);

		if ($code >= 200 && $code < 400) {
			$out = $response;
		} else {
			$body = wp_remote_retrieve_body($response);
			$out  = new WP_Error($code, $body);
		}

		return $out;
	}
}
