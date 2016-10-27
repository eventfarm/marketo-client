<?php
namespace EventFarm\Marketo\API;

use EventFarm\Marketo\RestClient\MarketoRestClient;

class Statuses
{
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function getStatuses(string $programChannel, array $options = array())
    {
        $endpoint = '/rest/asset/v1/channel/byName.json?name=' . $programChannel;

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
            print_r('Unable to get statuses: ' . $e);
        }
    }
}
