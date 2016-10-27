<?php
namespace EventFarm\Marketo;

use EventFarm\Marketo\Oauth\AccessToken;

interface TokenRefreshInterface
{
    public function tokenRefreshCallback(AccessToken $accessToken);
}
