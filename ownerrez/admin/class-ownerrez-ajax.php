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

    private $client;

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
        $this->client = null;
	}

	function get_client()
    {
        if ($this->client == null)
        {
            $apiRoot = get_option('ownerrez_apiRoot', null);
            $username = get_option('ownerrez_username', null);
            $token = get_option('ownerrez_token', null);

            if ($apiRoot == null || $username == null || $token == null)
            {
                return '{ \'exception\': \'Configuration incomplete. Go to ' . get_bloginfo('url') . '/wp-admin/options-general.php?page=ownerrez to complete plugin setup.\' }';
            }

            $this->client = new \OwnerRez\Api\Client($username, $token, $apiRoot);
        }

        return $this->client;
    }

	public function call()
    {
        $call = $_POST['call'];
        $verb = !empty($call['verb']) ? strtolower($call['verb']) : 'get';
        $get_resource = $call['resource'];
        $action = $call['action'];

        // This is useful for testing, but each ajax call should be restricted to a unqiue nonce for security
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "ownerrez_nonce_".$get_resource."_".$verb."_".$action)) {
            exit("Invalid request");
        }

        if (!is_string($this->get_client()))
        {
            $resource = $this->get_client()->$get_resource();

            try {
                echo json_encode([ 'status' => 'success', 'response' => json_decode($resource->request($verb, $action, $call['id'], $call['query'], $call['body'])) ]);
            }
            catch (\GuzzleHttp\Exception\ServerException $ex) {
                echo json_encode([ 'status' => 'error', 'exception' => $ex->__toString(), 'messages' => json_decode($ex->getResponse()->getBody())->messages ]);
            }
//            catch (Exception $ex) {
//                echo json_encode([ 'status' => 'error', 'exception' => $ex->__toString() ]);
//            }
        }

        wp_die(); // this is required to terminate immediately and return a proper response
    }
}
