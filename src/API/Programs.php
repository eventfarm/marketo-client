<?php
namespace Kristenlk\Marketo\API;

use GuzzleHttp\Client as Guzzle;

class Programs extends BaseClient {
    protected $programs = array();

    public function getPrograms(Array $options = array()):stdClass
    {
        if ($options) {
            $endpoint = '/rest/asset/v1/programs.json?maxReturn=200&offset=' . $options['offset'];
        } else {
            $endpoint = '/rest/asset/v1/programs.json?maxReturn=200';
        }

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse()

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>