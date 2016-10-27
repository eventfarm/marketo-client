<?php
namespace EventFarm\Marketo\API;

use EventFarm\Marketo\RestClient\MarketoRestClient;

class LeadFields
{
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function getLeadFields(array $options = array())
    {
        $endpoint = '/rest/v1/leads/describe.json';

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
            print_r('Unable to get lead fields: ' . $e);
        }
    }
}
