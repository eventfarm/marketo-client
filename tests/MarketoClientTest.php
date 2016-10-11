<?php
use Kristenlk\Marketo\Oauth\MarketoProviderInterface;
use Kristenlk\Marketo\RestClient\RestClientInterface;
use Kristenlk\Marketo\Oauth\AccessToken;
use Kristenlk\Marketo\MarketoClient;
// use Mockery;
use Psr\Http\Message\ResponseInterface;

class MarketoClientTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCampaignsSendsCorrectRequestWithoutOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $programName = 'My Program Name';

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/v1/campaigns.json?programName=' . $programName,
            $this->getAuthorizationHeader()
        );
        $marketoClient->campaigns()->getCampaigns($programName);
    }

    public function testGetCampaignsSendsCorrectRequestWithOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $programName = 'My Program Name';
        $nextPageToken = 'abc123';

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/v1/campaigns.json?programName=' . $programName . '&nextPageToken=' . $nextPageToken,
            $this->getAuthorizationHeader()
        );
        $marketoClient->campaigns()->getCampaigns($programName, ['nextPageToken' => $nextPageToken]);
    }

    public function testTriggerCampaignSendsCorrectRequestWithOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $campaignId = 123;
        $options = [
            'input' => [
                'leads' => [
                    [
                        'id' => 2931
                    ],
                    [
                        'id' => 1459
                    ]
                ]
            ]
        ];

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'post',
            '/rest/v1/campaigns/' . $campaignId . '/trigger.json',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'myAccessToken',
                    'Content-Type' => 'application/json'
                ],
                'json' => $options
            ]
        );
        $marketoClient->campaigns()->triggerCampaign($campaignId, $options);
    }

    public function testGetLeadFieldsSendsCorrectRequest()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/v1/leads/describe.json',
            $this->getAuthorizationHeader()
        );
        $marketoClient->leadFields()->getLeadFields();
    }

    public function testCreateOrUpdateLeadsSendsCorrectRequestWithOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $options = [
            'input' => [
                [
                    'email' => 'exampleemail1@example.com',
                    'firstName' => 'Example1',
                    'lastName' => 'Example2'
                ],
                [
                    'email' => 'exampleemail2@example.com',
                    'firstName' => 'Example2',
                    'lastName' => 'Example2'
                ]
            ]
        ];

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'post',
            '/rest/v1/leads.json',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'myAccessToken',
                    'Content-Type' => 'application/json'
                ],
                'json' => $options
            ]
        );
        $marketoClient->leads()->createOrUpdateLeads($options);
    }

    public function testUpdateLeadsProgramStatusSendsCorrectRequestWithOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $programId = 1234;
        $options = [
            'input' => [
                [
                    'id' => 2931
                ]
            ],
            'status' => 'Registered'
        ];

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'post',
            '/rest/v1/leads/programs/' . $programId . '/status.json',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . 'myAccessToken',
                    'Content-Type' => 'application/json'
                ],
                'json' => $options
            ]
        );
        $marketoClient->leads()->updateLeadsProgramStatus($programId, $options);
    }

    public function testGetLeadsByProgramSendsCorrectRequestWithoutOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $programId = 1234;

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/v1/leads/programs/' . $programId . '.json',
            $this->getAuthorizationHeader()
        );
        $marketoClient->leads()->getLeadsByProgram($programId);
    }

    public function testGetLeadsByProgramSendsCorrectRequestWithOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $programId = 1234;
        $nextPageToken = 'abc123';
        $options = [
            'fields' => 'firstName,lastName,email',
            'nextPageToken' => $nextPageToken
        ];

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/v1/leads/programs/' . $programId . '.json?fields=' . $options['fields'] . '&nextPageToken=' . $nextPageToken,
            $this->getAuthorizationHeader()
        );
        $marketoClient->leads()->getLeadsByProgram($programId, $options);
    }

    public function testGetPartitionsSendsCorrectRequest()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/v1/leads/partitions.json',
            $this->getAuthorizationHeader()
        );
        $marketoClient->partitions()->getPartitions();
    }

    public function testGetProgramsSendsCorrectRequestWithoutOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/asset/v1/programs.json?maxReturn=200',
            $this->getAuthorizationHeader()
        );
        $marketoClient->programs()->getPrograms();
    }

    public function testGetProgramsSendsCorrectRequestWithOptions()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $offset = '200';

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/asset/v1/programs.json?maxReturn=200&offset=' . $offset,
            $this->getAuthorizationHeader()
        );
        $marketoClient->programs()->getPrograms(['offset' => $offset]);
    }

    public function testGetStatusesSendsCorrectRequest()
    {
        // Arrange
        $marketoProvider = $this->getMarketoProviderMock();
        $response = $this->getResponseMock();
        $programChannel = 'Live Event';

        // Act/Assert
        $marketoClient = $this->getMarketoClientWithParameterAsserts(
            $response,
            $marketoProvider,
            'get',
            '/rest/asset/v1/channel/byName.json?name=' . $programChannel,
            $this->getAuthorizationHeader()
        );
        $marketoClient->statuses()->getStatuses($programChannel);
    }

    private function getAuthorizationHeader()
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . 'myAccessToken'
            ]
        ];
    }

    private function getMarketoProviderMock()
    {
        $provider = Mockery::mock(MarketoProviderInterface::class);
        $accessToken = Mockery::mock(AccessToken::class);
        $provider->shouldReceive('getAccessToken')
            ->andReturn($accessToken);
        $accessToken->shouldReceive('getToken')
            ->andReturn('myAccessToken');
        return $provider;
    }

    private function getResponseMock(
        int $responseCode = 200
    ) {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn($responseCode);
        $response->shouldReceive('getBody')
            ->andReturn($response);
        $response->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        return $response;
    }

    private function getFailureResponseMock(
        int $responseCode = 200
    ) {
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->andReturn($responseCode);
        $response->shouldReceive('getBody')
            ->andReturn($response);
        $response->shouldReceive('__toString')
            ->andReturn('{"result":[{}]}');
        return $response;
    }

    private function getMarketoClientWithParameterAsserts(
        ResponseInterface $response,
        MarketoProviderInterface $marketoProvider,
        string $method,
        string $endpoint,
        array $options
    ):MarketoClient {
        $restClient = Mockery::mock(RestClientInterface::class);
        $restClient->shouldReceive('request')
            ->andReturnUsing(function ($m, $e, $o) use ($method, $endpoint, $options, $response) {
                $this->assertEquals($method, $m);
                $this->assertEquals($endpoint, $e);
                $this->assertEquals($options, $o);
                return $response;
            })
            ->once();
        return MarketoClient::with(
            $restClient,
            $marketoProvider,
            'myAccessToken',
            1234567890,
            2345678901
        );
    }

    private function getMarketoClient(
        ResponseInterface $response,
        MarketoProviderInterface $marketoProvider
    ):MarketoClient {
        $restClient = Mockery::mock(RestClientInterface::class);
        $restClient->shouldReceive('request')
            ->andReturn($response)
            ->once();
        return MarketoClient::with(
            $restClient,
            $marketoProvider,
            'myAccessToken',
            1234567890,
            2345678901
        );
    }
}