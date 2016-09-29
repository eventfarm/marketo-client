<?php
namespace Kristenlk\Marketo\API;

use GuzzleHttp\Client as Guzzle;

class Programs extends BaseClient {
    public function getAllPrograms(){
        $endpoint = '/rest/asset/v1/programs.json?maxReturn=200';

        $this->request("get", $endpoint);
    }
}
?>