<?php
namespace Kristenlk\Marketo\Oauth;

use Kristenlk\OAuth2\Client\Provider\Marketo;

class KristenlkMarketoProvider implements MarketoProviderInterface
{
    /**
     * @var Marketo
     */
    public $marketo;

    public static function createDefaultProvider(
        string $clientId,
        string $clientSecret,
        string $baseUrl
    ) {
        return new self(
            new Marketo([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'baseUrl' => $baseUrl
            ])
        );
    }

    public function __construct(Marketo $marketo)
    {
        $this->marketo = $marketo;
    }

    /**
     * Requests an access token using a specified grant and option set.
     *
     * @param  mixed $grant
     * @param  array $options
     * @return AccessTokenInterface
     */
    public function getAccessToken($grant, array $options = []):AccessTokenInterface
    {
        $marketoAccessToken = $this->marketo->getAccessToken($grant, $options);
        return new AccessToken(
            $marketoAccessToken->getToken(),
            $marketoAccessToken->getExpires()
        );
    }
}
