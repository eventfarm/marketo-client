<?php
namespace Kristenlk\Marketo\API;

use Kristenlk\Marketo\RestClient\MarketoRestClient;

class Leads
{
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function createOrUpdateLeads(array $options = array())
    {
        $endpoint = '/rest/v1/leads.json';

        $requestOptions = [
            'headers' => [
                // 'Accepts' => 'application/json'
                'Content-Type' => 'application/json',
            ],
            'json' => []
        ];

        foreach ($options as $key => $value) {
            $requestOptions['json'][$key] = $value;
        }

        try {
            $response = $this->marketoRestClient->request('post', $endpoint, $requestOptions);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to create or update leads: ' . $e);
        }
    }

    public function updateLeadsProgramStatus(int $programId, array $options = array())
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

        try {
            $response = $this->marketoRestClient->request('post', $endpoint, $requestOptions);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to update leads\' program statuses: ' . $e);
        }
    }

    public function getLeadsByProgram(int $programId, array $options = array())
    {
        // Add &batchSize=1 to test batches of campaigns
        $endpoint = '/rest/v1/leads/programs/' . $programId . '.json';

        foreach ($options as $key => $value) {
            if (!empty($key)) {
                $endpoint = strpos($endpoint, '.json?') ? $endpoint . '&' : $endpoint . '?';
                $endpoint = $endpoint . $key . '=' . $value;
            }
        }

        try {
            $response = $this->marketoRestClient->request('get', $endpoint);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to get leads by program: ' . $e);
        }
    }
}
