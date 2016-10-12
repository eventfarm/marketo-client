<?php
namespace Kristenlk\Marketo\API;

use Kristenlk\Marketo\RestClient\MarketoRestClient;

class Programs
{
    /**
     * @var MarketoClientInterface
     */
    private $marketoRestClient;

    public function __construct(MarketoRestClient $marketoRestClient)
    {
        $this->marketoRestClient = $marketoRestClient;
    }

    public function getPrograms(array $options = array())
    {
        $endpoint = '/rest/asset/v1/programs.json?maxReturn=200';

        if (!empty($options['offset'])) {
            $endpoint = $endpoint . '&offset=' . $options['offset'];
        }

        try {
            $response = $this->marketoRestClient->request('get', $endpoint);
            return $this->marketoRestClient->getBodyObjectFromResponse($response);
        } catch (MarketoException $e) {
            print_r('Unable to get programs: ' . $e);
        }
    }
}
