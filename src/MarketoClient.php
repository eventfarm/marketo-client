<?php
namespace Kristenlk\Marketo;

use Kristenlk\Marketo\Oauth\AccessToken;
use Kristenlk\Marketo\Oauth\MarketoProviderInterface;
use Kristenlk\Marketo\Oauth\KristenlkMarketoProvider;
use Kristenlk\Marketo\API\Programs;
use Kristenlk\Marketo\API\Campaigns;
use Kristenlk\Marketo\API\LeadFields;
use Kristenlk\Marketo\API\Statuses;
use Kristenlk\Marketo\API\Leads;
use Kristenlk\Marketo\API\Partitions;
use Kristenlk\Marketo\RestClient\GuzzleRestClient;
use Kristenlk\Marketo\RestClient\RestClientInterface;
use Kristenlk\Marketo\RestClient\MarketoRestClient;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class MarketoClient
{
    /**
     * @var RestClientInterface
     */
    private $client;

    const DEFAULT_MAX_RETRY_REQUESTS = 2;
    const DEFAULT_TOKEN_REFRESH_OBJECT = null;

    public static function with(
        RestClientInterface $restClient,
        MarketoProviderInterface $marketoProvider,
        string $accessToken,
        int $tokenExpiresIn,
        int $tokenLastRefresh,
        TokenRefreshInterface $tokenRefreshObject = self::DEFAULT_TOKEN_REFRESH_OBJECT,
        int $maxRetryRequests = self::DEFAULT_MAX_RETRY_REQUESTS
    ) {
    
        return new self(
            $restClient,
            $marketoProvider,
            new AccessToken($accessToken, $tokenExpiresIn, $tokenLastRefresh),
            $tokenRefreshObject,
            $maxRetryRequests
        );
    }
    public static function withDefaults(
        string $accessToken,
        int $tokenExpiresIn,
        int $tokenLastRefresh,
        string $clientId,
        string $clientSecret,
        string $baseUrl,
        TokenRefreshInterface $tokenRefreshObject = self::DEFAULT_TOKEN_REFRESH_OBJECT,
        int $maxRetryRequests = self::DEFAULT_MAX_RETRY_REQUESTS
    ) {
        $restClient = GuzzleRestClient::createClient($baseUrl);
        $marketoProvider =
            KristenlkMarketoProvider::createDefaultProvider(
                $clientId,
                $clientSecret,
                $baseUrl
            );
        return new self(
            $restClient,
            $marketoProvider,
            new AccessToken($accessToken, $tokenExpiresIn, $tokenLastRefresh),
            $tokenRefreshObject,
            $maxRetryRequests
        );
    }
    private function __construct(
        RestClientInterface $restClient,
        MarketoProviderInterface $marketoProvider,
        AccessToken $accessToken,
        $tokenRefreshObject,
        int $maxRetryRequests
    ) {
        $this->client = new MarketoRestClient(
            $restClient,
            $marketoProvider,
            $accessToken,
            $tokenRefreshObject,
            $maxRetryRequests
        );
    }

    public function programs()
    {
        return new Programs($this->client);
    }

    public function campaigns()
    {
        return new Campaigns($this->client);
    }

    public function leadFields()
    {
        return new LeadFields($this->client);
    }

    public function statuses()
    {
        return new Statuses($this->client);
    }

    public function leads()
    {
        return new Leads($this->client);
    }

    public function partitions()
    {
        return new Partitions($this->client);
    }

    private function request(string $method, string $uri, array $options = []):ResponseInterface
    {
        $response = $this->client->request($method, $uri, $options);
        return $response;
    }
}
