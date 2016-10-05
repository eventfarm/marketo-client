<?php
namespace Kristenlk\Marketo\API;

use stdClass;

class Partitions extends BaseClient {
    public function getPartitions()//:stdClass
    {
        $endpoint = '/rest/v1/leads/partitions.json';

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse(). If there are errors, the status still looks to be 200

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>