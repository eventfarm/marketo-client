<?php
namespace Kristenlk\Marketo\API;

use Kristenlk\Marketo\RestClient\MarketoRestClient;

class Campaigns
{
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function getCampaigns(string $programName, array $options = array())
    {
        $endpoint = '/rest/v1/campaigns.json?programName=' . $programName;

        if (!empty($options['nextPageToken'])) {
            $endpoint = $endpoint . '&nextPageToken=' . $options['nextPageToken'];
        }

        try {
            $response = $this->marketoRestClient->request('get', $endpoint);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to get campaigns: ' . $e);
        }
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

        try {
            $response = $this->marketoRestClient->request('post', $endpoint, $requestOptions);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to trigger campaign: ' . $e);
        }
    }
}
