<?php
namespace EventFarm\Marketo\RestClient;

use EventFarm\Marketo\Oauth\AccessToken;
use EventFarm\Marketo\Oauth\MarketoProviderInterface;
use EventFarm\Marketo\Oauth\RetryAuthorizationTokenFailedException;
use EventFarm\Marketo\TokenRefreshInterface;
use EventFarm\Marketo\RestClient\RestClientInterface;
use Psr\Http\Message\ResponseInterface;

class MarketoRestClient
{
    /**
     * @var RestClientInterface
     */
    private $restClient;

    /**
     * @var MarketoProviderInterface
     */
    private $marketoProvider;

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var TokenRefreshInterface|null
     */
    private $tokenRefreshCallback;

    /**
     * @var int
     */
    private $maxRetryRequests;

    const TOKEN_INVALID = 601;
    const TOKEN_EXPIRED = 602;

    public function __construct(
        RestClientInterface $restClient,
        MarketoProviderInterface $marketoProvider,
        AccessToken $accessToken,
        TokenRefreshInterface $tokenRefreshCallback = null,
        int $maxRetryRequests
    ) {
        $this->restClient = $restClient;
        $this->marketoProvider = $marketoProvider;
        $this->accessToken = $accessToken;
        $this->tokenRefreshCallback = $tokenRefreshCallback;
        $this->maxRetryRequests = $maxRetryRequests;
    }

    public function request(string $method, string $uri = '', array $options = []):ResponseInterface
    {
        return $this->retryRequest(
            $method,
            $uri,
            $options
        );
    }

    private function getAccessToken():string
    {
        return $this->accessToken->getToken();
    }

    private function isTokenValid($responseBody):bool
    {
        /* Depending on the endpoint, the JSON Marketo returns will always contain an errors key (like getPrograms
        does) or will only contain an errors key if there are errors (like getCampaigns does) */
        if (property_exists($responseBody, "errors")) {
            if (!empty($responseBody->errors)) {
                $errorCodes = array(self::TOKEN_INVALID, self::TOKEN_EXPIRED);

                foreach ($responseBody->errors as $error) {
                    if (in_array($error->code, $errorCodes)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    private function isResponseAuthorized(ResponseInterface $response):bool
    {
        return ! ($response->getStatusCode() === 401);
    }

    private function refreshAccessToken()
    {
        $tokenResponse = $this->marketoProvider->getAccessToken('client_credentials');

        if (!empty($this->tokenRefreshCallback)) {
            $this->tokenRefreshCallback->tokenRefreshCallback($tokenResponse);
        }

        $this->accessToken = $tokenResponse;
    }

    private function mergeOptions(array $options):array
    {
        $defaultOptions = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken()
            ]
        ];

        $options = array_replace_recursive($options, $defaultOptions);

        return $options;
    }

    private function retryRequest(string $method, string $uri, array $options):ResponseInterface
    {
        $attempts = 0;
        do {
            if (time() >= $this->accessToken->getExpires() - 300) {
                $this->refreshAccessToken();
            }

            $options = $this->mergeOptions($options);
            $response = $this->restClient->request($method, $uri, $options);
            $responseBody = json_decode($response->getBody()->__toString());

            $isAuthorized = $this->isResponseAuthorized($response);
            $isTokenValid = $this->isTokenValid($responseBody);

            if (!$isAuthorized || !$isTokenValid) {
                $this->refreshAccessToken();
            }

            $attempts++;
        } while ((!$isAuthorized || !$isTokenValid) && $attempts < $this->maxRetryRequests);

        if (!$isAuthorized || !$isTokenValid) {
            throw new RetryAuthorizationTokenFailedException(
                'Max retry limit of ' . $this->maxRetryRequests . 'has been reached. Retrieving access token failed.'
            );
        }

        return $response;
    }

    public function getBodyObjectFromResponse(ResponseInterface $request)
    {
        return (object) json_decode($request->getBody()->__toString());
    }
}
