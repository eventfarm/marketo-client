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

    public function triggerCampaign(int $campaignId, array $options = array())//:stdClass
    {
        $endpoint = '/rest/v1/campaigns/' . $campaignId . '/trigger.json';

        $requestOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => []
        ];

        foreach ($options as $key => $value) {
            $requestOptions['json'][$key] = $value;
        }

        $response = $this->request('post', $endpoint, $requestOptions);

        // If $response->getStatusCode() !== 200, throw an error - don't call getBodyObjectFromResponse(). If there are errors, the status still looks to be 200

        $responseBody = $this->getBodyObjectFromResponse($response);

        return $responseBody;
    }
}
?>
