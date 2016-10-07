<?php
namespace Kristenlk\Marketo;

use Kristenlk\Marketo\Oauth\AccessToken;

interface TokenRefreshInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
