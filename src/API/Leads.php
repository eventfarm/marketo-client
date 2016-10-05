<?php
namespace Kristenlk\Marketo\API;

use stdClass;

class Leads extends BaseClient {
    public function createOrUpdateLeads(array $options = array())//:stdClass
    {
        $endpoint = '/rest/v1/leads.json';

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

    public function updateLeadsProgramStatus(int $programId, array $options = array())//:stdClass
    {
        $endpoint = '/rest/v1/leads/programs/' . $programId . '/status.json';

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

    public function getLeadsByProgram(int $programId, array $options = array())//:stdClass
    {
        // Add &batchSize=1 to test batches of campaigns
        $endpoint = '/rest/v1/leads/programs/' . $programId . '.json?batchSize=1';

        // Once I get rid of batchSize, handle the question mark vs. ampersand thing

        if (!empty($options['fields'])) {
            $endpoint = $endpoint . '&fields=' . $options['fields'];
        }

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