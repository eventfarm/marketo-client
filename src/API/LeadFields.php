<?php
namespace Kristenlk\Marketo\API;

use stdClass;

class LeadFields extends BaseClient {
    public function getLeadFields()//:stdClass
    {
        $endpoint = '/rest/v1/leads/describe.json';

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse(). If there are errors, the status still looks to be 200

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>