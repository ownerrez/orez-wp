<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/ajax
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/ajax
 * @author     OwnerRez Inc <dev@ownerreservations.com>
 */
class OwnerRez_Ajax
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

	private $api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $ownerrez       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($ownerrez, $version)
	{
		$this->ownerrez = $ownerrez;
		$this->version = $version;
        $this->api = new OwnerRez_ApiWrapper();
	}

    public function call_nopriv()
    {
        // TODO: make this a setting
        $allowedRequestKeys = array(
            "quotes_test_",
            "quotes_post_",
            "guests_post_"
        );

        $this->execute_request($allowedRequestKeys);
    }

	public function call()
    {
        $this->execute_request(null);
    }

    private function execute_request($allowedRequestKeys)
    {
        $call = $_POST['or_call'];
        $verb = !empty($call['verb']) ? $call['verb'] : 'get';
        $get_resource = $call['resource'];
        $action = $call['action'];

        if ($allowedRequestKeys != null)
        {
            $requestKey = strtolower($get_resource . "_" . $verb . "_" . $action);

            if (!in_array($requestKey, $allowedRequestKeys)) {
                exit ("404 Not Found");
            }
        }

        try {
            $response = $this->api->send_request($get_resource, $verb, $action, $call['id'], $call['query'], $call['body']);
            echo json_encode(['status' => 'success', 'response' => $response]);
        }
        catch (\GuzzleHttp\Exception\ServerException $ex) {
            echo json_encode([ 'status' => 'error', 'exception' => $ex->__toString(), 'messages' => json_decode($ex->getResponse()->getBody())->messages ]);
        }

        wp_die(); // this is required to terminate immediately and return a proper response
    }
}
