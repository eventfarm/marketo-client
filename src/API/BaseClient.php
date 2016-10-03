<?php
namespace Kristenlk\Marketo\API;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Kristenlk\OAuth2\Client\Provider\Marketo;
use Kristenlk\OAuth2\Client\Token\AccessToken;

class BaseClient
{
    private $authParams;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $accessToken;
    protected $tokenRefreshObject;
    protected $guzzle;
    protected $retryCount = 4;

    const TOKEN_INVALID = 601;
    const TOKEN_EXPIRED = 602;

    public function __construct($clientId, $clientSecret, $baseUrl, $tokenRefreshObject, $accessToken)
    {
        $this->clientId           = $clientId;
        $this->clientSecret       = $clientSecret;
        $this->baseUrl            = $baseUrl;
        $this->tokenRefreshObject = $tokenRefreshObject;
        $this->accessToken        = $accessToken;
        $this->guzzle             = new Guzzle(['base_uri' => $this->baseUrl]);

        $this->authParams = [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'baseUrl' => $this->baseUrl
        ];
    }

    public function setRetryCount(int $count)
    {
        if ($count > 1 && $count < 50) {
            $this->retryCount = $count;
        }
    }

    private function isTokenValid($errors)
    {
        $errorCodes = array(self::TOKEN_INVALID, self::TOKEN_EXPIRED);

        if ($errors) {
            foreach($errors as $error) {
                if (in_array($error->code, $errorCodes)) {
                    $this->refreshAccessToken();
                    return false;
                } else {
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    private function isResponseAuthorized(int $statusCode):bool
    {
        if ($statusCode === 401) {
            $this->refreshAccessToken();
            return false;
        } else {
            return true;
        }
    }

    private function refreshAccessToken()
    {
        $this->requestMarketoAccessToken();
    }

    public function request(string $method, string $uri, array $options = []):Response
    {
        $count = 0;

        do {
            $defaultOptions = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken
                ]
            ];

            $overrideOptions = array_merge($options, $defaultOptions);

            $response = $this->guzzle->request($method, $uri, $overrideOptions);
            $responseBody = json_decode($response->getBody()->__toString());

            $tokenValid = $this->isTokenValid($responseBody->errors);
            $responseAuthorized = $this->isResponseAuthorized($response->getStatusCode());

            $count++;
        } while($count <= $this->retryCount && (!$responseAuthorized || !$tokenValid));

        return $response;
    }

    private function requestMarketoAccessToken()
    {
        $provider = new Marketo($this->authParams);
        $tokenResponse = $provider->getAccessToken('client_credentials');
        $accessToken = $tokenResponse->getToken();
        $this->accessToken = $accessToken;

        if (!empty($this->tokenRefreshObject)) {
            $this->tokenRefreshObject->tokenRefreshCallback($tokenResponse);
        }

        return $accessToken;
    }

    protected function getBodyObjectFromResponse(ResponseInterface $request)
    {
        return (object) json_decode($request->getBody()->__toString());
    }
}