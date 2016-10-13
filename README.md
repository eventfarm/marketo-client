# marketo-client

[![Travis](https://img.shields.io/travis/kristenlk/marketo-client.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/kristenlk/marketo-client)
[![Downloads](https://img.shields.io/packagist/dt/kristenlk/marketo-client.svg?style=flat-square)](https://packagist.org/packages/kristenlk/marketo-client)
[![Packagist](https://img.shields.io/packagist/l/kristenlk/marketo-client.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/kristenlk/marketo-client)
[![Code Climate](https://codeclimate.com/github/kristenlk/marketo-client/badges/gpa.svg)](https://codeclimate.com/github/kristenlk/marketo-client)
[![Test Coverage](https://codeclimate.com/github/kristenlk/marketo-client/badges/coverage.svg)](https://codeclimate.com/github/kristenlk/marketo-client/coverage)

This package provides an interface for interacting with the Marketo REST API.

## Installation

```
$ composer require kristenlk/marketo-client
```

Or.

Add the following lines to your ``composer.json`` file.

```json
{
    "require": {
        "kristenlk/marketo-client": "dev-master"
    }
}
```

```bash
$ composer install
```

## Project Defaults

In order to get you up and running as easily as possible, we provide default implementations of a REST client and Marketo provider to use in combination with this package. 
* We've chosen to use [Guzzle](https://github.com/guzzle/guzzle) for sending HTTP requests
* We've chosen to use [The PHP League's Oauth Client](https://github.com/thephpleague/oauth2-client) and my [Marketo provider](https://github.com/kristenlk/oauth2-marketo) for Marketo authentication and token refresh.

### Guzzle REST Client

Our REST client implements the PSR-7 HTTP message interface.

You can either use the provided [GuzzleRestClient](./src/RestClient/GuzzleRestClient.php) or have your own that implements our [RestClientInterface](./src/RestClient/RestClientInterface.php).

### KristenlkMarketoProvider

Our default Marketo provider is my [Marketo Provider](https://github.com/kristenlk/oauth2-marketo) library.

You can either use the provided [KristenlkMarketoProvider](./src/Oauth/KristenlkMarketoProvider.php) or use your own that implements our [MarketoProviderInterface](./src/Oauth/MarketoProviderInterface.php).

## Example Client Implementation

```php
<?php
namespace App;

use Kristenlk\Marketo\Oauth\AccessToken;
use Kristenlk\Marketo\MarketoClient;
use Kristenlk\Marketo\TokenRefreshInterface;

class DemoMarketoClient implements TokenRefreshInterface
{
    public function getMarketoClient():MarketoClient
    {
        if (empty($this->marketo)) {
            $this->marketo = MarketoClient::withDefaults(
                'ACCESS_TOKEN',
                'TOKEN_EXPIRES_IN', // when the current access token expires (as a UNIX timestamp)
                'TOKEN_LAST_REFRESH', // when the current access token was last refreshed (as a UNIX timestamp)
                'CLIENT_ID',
                'CLIENT_SECRET',
                'BASE_URL',
                $this // TokenRefreshInterface
            );
        }
        return $this->marketo;
    }

    public function tokenRefreshCallback(AccessToken $token)
    {
        // CALLBACK FUNCTION TO STORE THE REFRESHED $token TO PERSISTANCE LAYER
    }
}
```

## Usage

### Campaigns

#### Get Campaigns
[Docs](http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Campaigns/getCampaignsUsingGET)
Returns a list of campaign records. Refer to the docs for the full list of options.

`public function getCampaigns(array $options = array())`

```php
<?php
$demoMarketoClient = new DemoMarketoClient()->getMarketoClient();

$options = [
  "programName" => "My Marketo Program",
  "batchSize" => 10
];

$campaigns = $demoMarketoClient->campaigns()->getCampaigns($options);
// getCampaigns() can also be called without options.
// $campaigns = { ... }
```

#### Trigger Campaign
[Docs](http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Campaigns/triggerCampaignUsingPOST)
Passes a set of leads to a trigger campaign to run through the campaign's flow. Refer to the docs for the full list of options.

* A **campaignId** and an array of options that includes an **input** key (mapped to an array that contains arrays of lead data) must be passed to `triggerCampaign()`.

`public function triggerCampaign(int $campaignId, array $options)`

```php
<?php
$demoMarketoClient = new DemoMarketoClient()->getMarketoClient();

$campaignId = 1029;
$options = [
    "input" => [
        "leads" => [
            [
                "id" => 1234
            ]
        ]
    ]//, additional options
];

$campaign = $demoMarketoClient->campaigns()->triggerCampaign($campaignId, $options);
// $campaign = { ... }
```

### Lead Fields

#### Get Lead Fields
[Docs](http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Leads/describeUsingGET_2)
Returns metadata about lead objects in the target instance, including a list of all fields available for interaction via the APIs.

`public function getLeadFields()`

```php
<?php
$demoMarketoClient = new DemoMarketoClient()->getMarketoClient();
$leadFields = $demoMarketoClient->leadFields()->getLeadFields();
// $leadFields = { ... }
```

### Leads

#### Create or Update Leads
[Docs](http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Leads/syncLeadUsingPOST)
Syncs a list of leads to the target instance. Refer to the docs for the full list of options.

* An array of options that includes an **input** key (mapped to an array that contains arrays of lead data) must be passed to `createOrUpdateLeads()`.

`public function createOrUpdateLeads(array $options)`

By default, Marketo sets the type of sync operation (`action`) to `createOrUpdate` and the `lookupField` to `email`. If using those defaults:
- Email is not required; if an email is not included in a lead array, Marketo will create a lead without an email.
- When an email is included, Marketo will search for existing leads with that email. If one is found, Marketo will update the found lead with the data sent; if one is not found, Marketo will create a new lead with the data sent.
- NOTE: If Marketo finds more than one lead with the same email, none of the leads will be updated.

```php
<?php
$demoMarketoClient = new DemoMarketoClient()->getMarketoClient();

$options = [
    "input" => [
        [
            "email" => "email1@example.com",
            "firstName" => "Example1First",
            "lastName" => "Example1Last"
        ],
        [
            "email" => "email2@example.com",
            "firstName" => "Example2First",
            "lastName" => "Example2Last"
        ]
    ]//, additional options
];

$leads = $demoMarketoClient->leads()->createOrUpdateLeads($options);
// $leads = { ... }
```

#### Update Leads' Program Status
[Docs](http://developers.marketo.com/rest-api/endpoint-reference/lead-database-endpoint-reference/#!/Leads/changeLeadProgramStatusUsingPOST)
Changes the program status of a list of leads in a target program. Refer to the docs for the full list of options.

* A **programId** and an array of options that includes an **input** key (mapped to an array that contains arrays of lead data) and a **status** key (mapped to a program status) must be passed to `updateLeadsProgramStatus()`.

`public function updateLeadsProgramStatus(int $programId, array $options)`

```php
<?php
$demoMarketoClient = new DemoMarketoClient()->getMarketoClient();

$programId = 1234;
$options = [
    "input" => [
        [
            "id" => 1111
        ]
    ],
    "status" => "Registered"
];

$leads = $demoMarketoClient->leads()->updateLeadsProgramStatus($programId, $options);
// $leads = { ... }
```
