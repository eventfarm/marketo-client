<?php
namespace Kristenlk;

use Kristenlk\Marketo\API\Programs;

class MarketoClient
{
    private $authParams;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $accessToken;
    protected $authCallback;

    public function __construct(array $attributes = [])
    {
        $this->clientId     = $attributes['clientId'];
        $this->clientSecret = $attributes['clientSecret'];
        $this->baseUrl      = $attributes['baseUrl'];
        $this->authCallback = $attributes['authCallback'];

        if (isset($attributes['accessToken'])) {
            $this->accessToken = $attributes['accessToken'];
        }
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

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAuthCallback()
    {
        return $this->authCallback;
    }


    // private function isResponseAuthorized(int $statusCode):bool
    // {
    //     if ($statusCode === 401) {
    //         $this->refreshAccessToken();
    //         return false;
    //     } else {
    //         return true;
    //     }
    // }

    // private function requestMarketoAccessToken()
    // {
    //     // $provider = new Salesforce($this->auth);
    //     // $authorizationUrl = $provider->getAuthorizationUrl();
    //     // header('Location: ' . $authorizationUrl);
    //     // exit;
    //     // print_r($this);
    //     // print_r($this->authParams);
    //     $provider = new Marketo($this->authParams); // Instantiate an oauth2-marketo provider
    //     // print_r("hi hi hi hi hi");
    //     // print_r($provider);
    //     $tokenResponse = $provider->getAccessToken('client_credentials'); // Request an access token, get back some JSON
    //     $accessToken = $tokenResponse->getToken(); // Grab the access token itself
    //     $this->setAccessToken($accessToken); // Set the access token on the MarketoClient
    //     return $accessToken;
    // }

    // So what happens is someone can instantiate a client, and then just call $client->send("blah blah").
    // send() is a method on the Message, not the Client.
    // The send() method takes "blah blah", puts it on the Message,
    // and then passes that Message back to the client - $this->client->sendMessage($this).
    // Back in the client's sendMessage() method, first we prepare the payload (preparePayload()).
    // 

// // I want to be able to do something like this:

// $contacts = $hubspot->contacts()->get_all_contacts(array(
//     'count' => 5, // defaults to 20
//     'property' => 'firstname', // only get the specified properties
//     'vidOffset' => '50' // contact offset used for paging
// ));

    // private function refreshAccessToken():AccessToken
    // {
    //     $this->requestMarketoAccessToken();
    // }


    // So for something like $client->programs()->getPrograms(), getPrograms() will call request().
    // request() is where the refreshing of tokens will take place.

    // public function request(string $method, string $uri, array $options = []):Response
    // {
    //     $client = new Client(); // instantiate new Guzzle client
    //     $accessToken = $this->getAccessToken(); // This is our existing access token

    //     $defaultOptions = [
    //         'headers' => [
    //             'Authorization' => 'Bearer ' . $accessToken
    //         ]
    //     ];

    //     $overrideOptions = array_merge($defaultOptions, $options);

    //     $count = 1;

    //     do {
    //         $response = $client->request($method, $uri, $overrideOptions);
    //         $success = $this->isResponseAuthorized($response->getStatusCode());
    //         $count++;
    //     } while(!$success || $count >= 4);
    //     return $response;
    // }

    public function programs()
    {
        return new Programs($this->clientId, $this->clientSecret, $this->baseUrl, $this->authCallback, $this->accessToken);
    }

}
