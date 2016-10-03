<?php
namespace Kristenlk\Marketo\API;

use Kristenlk\OAuth2\Client\Token\AccessToken;

interface TokenRefreshCallbackInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
