<?php
namespace Kristenlk\Marketo\API;

use stdClass;

class Statuses extends BaseClient {
    public function getStatuses(string $programChannel)//:stdClass
    {
        $endpoint = '/rest/asset/v1/channel/byName.json?name=' . $programChannel;

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse(). If there are errors, the status still looks to be 200

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>