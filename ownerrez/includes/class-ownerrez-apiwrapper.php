<?php


class OwnerRez_ApiWrapper
{
    private $client;

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

    function get_resource($resourceName)
    {
        $c = $this->get_client();

        if (!is_string($c))
            return $this->get_client()->$resourceName();
        else
            throw new Exception($c);
    }

    function send_request($resourceName, $verb, $action, $id, $query, $body)
    {
        $r = $this->get_resource($resourceName);

        if (!is_string($r)) {

            #todo: cache response if cachable
            $response = $r->request($verb, $action, $id, $query, $body);

            return $response;
        }
        else {
            throw new Exception($r);
        }
    }
}