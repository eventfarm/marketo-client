<?php
namespace Kristenlk\Marketo\API;

use stdClass;

class Campaigns extends BaseClient {
    public function getCampaigns(string $programName, array $options = array())//:stdClass
    {
        $endpoint = '/rest/v1/campaigns.json?programName=' . $programName;

        if (!empty($options['nextPageToken'])) {
            $endpoint = $endpoint . '&nextPageToken=' . $options['nextPageToken'];
        }

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse(). If there are errors, the status still looks to be 200

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>