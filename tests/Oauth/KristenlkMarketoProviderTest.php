<?php
namespace Kristenlk\Marketo\Tests\Oauth;

use Kristenlk\Marketo\Oauth\KristenlkMarketoProvider;
use League\OAuth2\Client\Token\AccessToken;
use Mockery;
use Kristenlk\OAuth2\Client\Provider\Marketo;

class KristenlkMarketoProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testLeagueAccessTokenFacade()
    {
        $myAccessToken = 'myAccessToken';
        $myExpiresIn = 1234567890;
        $myLastRefresh = 2345678901;
        $theLeagueAccessToken = Mockery::mock(AccessToken::class);
        $theLeagueAccessToken->shouldReceive('getToken')->andReturn($myAccessToken);
        $theLeagueAccessToken->shouldReceive('getExpires')->andReturn($myExpiresIn);
        $theLeagueAccessToken->shouldReceive('getLastRefresh')->andReturn($myLastRefresh);
        $marketoProvider = Mockery::mock(Marketo::class);
        $marketoProvider->shouldReceive('getAccessToken')
            ->andReturn($theLeagueAccessToken);
        $kristenlkMarketoProvider = new KristenlkMarketoProvider($marketoProvider);
        $accessToken = $kristenlkMarketoProvider->getAccessToken('client_credentials');
        $this->assertInstanceOf(\Kristenlk\Marketo\Oauth\AccessToken::class, $accessToken);
        $this->assertSame($myAccessToken, $accessToken->getToken());
    }
}
