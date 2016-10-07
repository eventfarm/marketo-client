<?php
namespace Kristenlk\Marketo\Oauth;

/**
 * An exception for when the max retry limit is reached when trying to
 * refresh the access token when making a request to Marketo.
 */
class RetryAuthorizationTokenFailedException extends \Exception
{

}