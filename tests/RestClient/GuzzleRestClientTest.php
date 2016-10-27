<?php
namespace EventFarm\Marketo\Tests\RestClient;

use EventFarm\Marketo\RestClient\GuzzleRestClient;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class GuzzleRestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGuzzleRestClientFacade()
    {
        $client = Mockery::mock(\GuzzleHttp\Client::class);
        $response = Mockery::mock(ResponseInterface::class);
        $guzzleRestClient = new GuzzleRestClient(
            $client
        );
        $client->shouldReceive('request')
        ->andReturn($response);
        $guzzleRestClient->request('GET', 'limits');
    }
}