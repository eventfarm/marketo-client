<?php
namespace Kristenlk\Marketo;

use GuzzleHttp\Client as Guzzle;
use RuntimeException;

class Client
{

    // This class should take a client id, client secret, base url, access token, and authentication callback.
        // If no access token is passed in, it should authenticate.
        // If access token is invalid, it should authenticate.

    /**
     * The Marketo service's client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The Marketo service's client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The URL that is used as a base URL for all calls
     * made to the Marketo REST API.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * The Marketo service's current access token, if
     * one exists.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * A method that will be called after a successful
     * authentication / reauthentication.
     *
     * @var string
     */
    protected $authCallback;

    /**
     * The Guzzle HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Instantiate a new Client.
     *
     * @param string $endpoint
     * @param array $attributes
     * @return void
     */
    public function __construct($endpoint, array $attributes = [], Guzzle $guzzle = null)
    {
        $this->endpoint     = $endpoint;
        $this->clientId     = $attributes['clientId'];
        $this->clientSecret = $attributes['clientSecret'];
        $this->baseUrl      = $attributes['baseUrl'];
        $this->authCallback = $attributes['authCallback'];
        $this->guzzle       = $guzzle ?: new Guzzle;
    }
}
