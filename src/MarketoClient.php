<?php
namespace EventFarm\Marketo;

use EventFarm\Marketo\Oauth\AccessToken;
use EventFarm\Marketo\Oauth\MarketoProviderInterface;
use EventFarm\Marketo\Oauth\KristenlkMarketoProvider;
use EventFarm\Marketo\API\Programs;
use EventFarm\Marketo\API\Campaigns;
use EventFarm\Marketo\API\LeadFields;
use EventFarm\Marketo\API\Statuses;
use EventFarm\Marketo\API\Leads;
use EventFarm\Marketo\API\Partitions;
use EventFarm\Marketo\RestClient\GuzzleRestClient;
use EventFarm\Marketo\RestClient\RestClientInterface;
use EventFarm\Marketo\RestClient\MarketoRestClient;
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
