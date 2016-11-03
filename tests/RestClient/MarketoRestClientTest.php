<?php
namespace EventFarm\Marketo\Tests\RestClient;

use EventFarm\Marketo\Oauth\AccessToken;
use EventFarm\Marketo\Oauth\RetryAuthorizationTokenFailedException;
use EventFarm\Marketo\Oauth\KristenlkMarketoProvider;
use EventFarm\Marketo\RestClient\GuzzleRestClient;
use EventFarm\Marketo\RestClient\MarketoRestClient;
use EventFarm\Marketo\TokenRefreshInterface;
use GuzzleHttp\Psr7;
use Mockery;
use Psr\Http\Message\ResponseInterface;

class MarketoRestClientTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionIsThrownWhenClientRetriesMoreThanMaxRetry()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(KristenlkMarketoProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $accessToken->shouldReceive('getLastRefresh')
            ->andReturn(1234567890);
        $accessToken->shouldReceive('getExpires')
            ->andReturn(1000);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshInterface::class);
        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $failedResponse = Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
            ->andReturn(401);
        $failedResponse->shouldReceive('getBody')
            ->andReturn($failedResponse);
        $failedResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $restClient->shouldReceive('request')
            ->andReturn($failedResponse)
            ->times(3);
        $maxRetry = 3;
        $marketoProvider = new MarketoRestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );
        $this->expectException(RetryAuthorizationTokenFailedException::class);
        $marketoProvider->request('GET', '/example/getExample');
    }

    public function testFailWith401ThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(KristenlkMarketoProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $accessToken->shouldReceive('getLastRefresh')
            ->andReturn(1234567890);
        $accessToken->shouldReceive('getExpires')
            ->andReturn(1000);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshInterface::class);
        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $failedResponse = Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
                       ->andReturn(401);
        $failedResponse->shouldReceive('getBody')
            ->andReturn($failedResponse);
        $failedResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $successResponse = Mockery::mock(ResponseInterface::class);
        $successResponse->shouldReceive('getStatusCode')
                        ->andReturn(200);
        $successResponse->shouldReceive('getBody')
            ->andReturn($successResponse);
        $successResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $restClient->shouldReceive('request')
                   ->andReturn($failedResponse)
                   ->times(2);
        $restClient->shouldReceive('request')
                   ->andReturn($successResponse)
                   ->once();
        $maxRetry = 3;
        $marketoProvider = new MarketoRestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );
        $response = $marketoProvider->request('GET', '/example/getExample');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testFailWithMarketoErrorThenRetryAndSucceedBeforeMaxRetryLimit()
    {
        $restClient = Mockery::mock(GuzzleRestClient::class);
        $provider = Mockery::mock(KristenlkMarketoProvider::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $accessToken->shouldReceive('getLastRefresh')
            ->andReturn(1234567890);
        $accessToken->shouldReceive('getExpires')
            ->andReturn(1000);
        $tokenRefreshCallback = Mockery::mock(TokenRefreshInterface::class);
        $tokenRefreshCallback->shouldReceive('tokenRefreshCallback');
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('MOCKACCESSTOKEN');
        $failedResponse = Mockery::mock(ResponseInterface::class);
        $failedResponse->shouldReceive('getStatusCode')
                       ->andReturn(200);
        $failedResponse->shouldReceive('getBody')
            ->andReturn($failedResponse);
        $failedResponse->shouldReceive('__toString')
            ->andReturn('{"errors":[{"code": 601, "message": "Access token invalid"}]}');
        $successResponse = Mockery::mock(ResponseInterface::class);
        $successResponse->shouldReceive('getStatusCode')
                        ->andReturn(200);
        $successResponse->shouldReceive('getBody')
            ->andReturn($successResponse);
        $successResponse->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        $restClient->shouldReceive('request')
                   ->andReturn($failedResponse)
                   ->times(2);
        $restClient->shouldReceive('request')
                   ->andReturn($successResponse)
                   ->once();
        $maxRetry = 3;
        $marketoProvider = new MarketoRestClient(
            $restClient,
            $provider,
            $accessToken,
            $tokenRefreshCallback,
            $maxRetry
        );
        $response = $marketoProvider->request('GET', '/example/getExample');
        $this->assertSame(200, $response->getStatusCode());
    }
}
