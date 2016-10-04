<?php
namespace Kristenlk;

use Kristenlk\Marketo\API\Programs;
use Kristenlk\Marketo\API\Campaigns;
use Kristenlk\Marketo\API\LeadFields;
use Kristenlk\Marketo\API\Statuses;
use Kristenlk\Marketo\API\Leads;
use Kristenlk\Marketo\API\TokenRefreshCallbackInterface;
use Kristenlk\OAuth2\Client\Token\AccessToken;
use GuzzleHttp\Client as Guzzle;

class MarketoClient
{
    private $authParams;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $accessToken;
    protected $tokenRefreshObject;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $baseUrl,
        TokenRefreshCallbackInterface $tokenRefreshObject = null,
        string $accessToken)
    {
        $this->clientId           = $clientId;
        $this->clientSecret       = $clientSecret;
        $this->baseUrl            = $baseUrl;
        $this->tokenRefreshObject = $tokenRefreshObject;
        $this->guzzle             = new Guzzle(['base_uri' => $this->baseUrl]);

        if (isset($accessToken)) {
            $this->accessToken = $accessToken;
        }

        $this->authParams = [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'baseUrl' => $this->baseUrl
        ];
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        } else {
            $this->requestMarketoAccessToken();
        }
    }

    public function programs()
    {
        return new Programs($this->clientId, $this->clientSecret, $this->baseUrl, $this->tokenRefreshObject, $this->accessToken);
    }

    public function campaigns()
    {
        return new Campaigns($this->clientId, $this->clientSecret, $this->baseUrl, $this->tokenRefreshObject, $this->accessToken);
    }

    public function leadFields()
    {
        return new LeadFields($this->clientId, $this->clientSecret, $this->baseUrl, $this->tokenRefreshObject, $this->accessToken);
    }

    public function statuses()
    {
        return new Statuses($this->clientId, $this->clientSecret, $this->baseUrl, $this->tokenRefreshObject, $this->accessToken);
    }

    public function leads()
    {
        return new Leads($this->clientId, $this->clientSecret, $this->baseUrl, $this->tokenRefreshObject, $this->accessToken);
    }
}
