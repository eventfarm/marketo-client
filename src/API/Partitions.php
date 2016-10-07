<?php
namespace Kristenlk\Marketo\API;

use Kristenlk\Marketo\RestClient\MarketoRestClient;

class Partitions {
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function getPartitions()
    {
        $endpoint = '/rest/v1/leads/partitions.json';

        try {
            $response = $this->marketoRestClient->request('get', $endpoint);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to get partitions: ' . $e);
        }
    }
}
?>