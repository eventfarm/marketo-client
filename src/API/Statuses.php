<?php
namespace Kristenlk\Marketo\API;

use Kristenlk\Marketo\RestClient\MarketoRestClient;

class Statuses {
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function getStatuses(string $programChannel)
    {
        $endpoint = '/rest/asset/v1/channel/byName.json?name=' . $programChannel;

        try {
            $response = $this->marketoRestClient->request('get', $endpoint);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to get statuses: ' . $e);
        }
    }
}
?>