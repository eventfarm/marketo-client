<?php
namespace Kristenlk\Marketo\API;

use stdClass;

class Programs extends BaseClient {
    public function getPrograms(array $options = array())//:stdClass
    {
        $endpoint = '/rest/asset/v1/programs.json?maxReturn=200';

        if (!empty($options['offset'])) {
            $endpoint = $endpoint . '&offset=' . $options['offset'];
        }

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse(). If there are errors, the status still looks to be 200

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>