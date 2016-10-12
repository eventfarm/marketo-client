<?php
namespace Kristenlk\Marketo\RestClient;

use Kristenlk\Marketo\RestClient\RestClientInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class GuzzleRestClient implements RestClientInterface
{
    public static function createClient(string $baseUrl)
    {
        return new self(
            new Client(
                [
                    'http_errors' => false,
                    'base_uri' => $baseUrl
                ]
            )
        );
    }

    /**
     * @var GuzzleClient
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function request(string $method, string $uri = '', array $options = []):ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}
