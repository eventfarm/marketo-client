<?php
namespace Kristenlk\Marketo\Oauth;

interface AccessTokenInterface
{
    public function getToken():string;
    public function getLastRefresh():int;
    public function getExpiresIn():int;
}
