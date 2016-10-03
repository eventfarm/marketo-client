<?php
namespace Kristenlk\Marketo\API;

use GuzzleHttp\Client as Guzzle;

class Programs extends BaseClient {
    protected $programs = array();

    public function getPrograms(Array $options = array()){
        if ($options) {
            $endpoint = '/rest/asset/v1/programs.json?maxReturn=200&offset=' . $options['offset'];
        } else {
            $endpoint = '/rest/asset/v1/programs.json?maxReturn=200';
        }

        $response = $this->request("get", $endpoint);

        // If $response->getStatusCode() !== 200, throw an error
        // If $responseBody->errors, return errors - don't iterate through $responseBody->result

        $responseBody = $this->getBodyObjectFromResponse($response);

        foreach ($responseBody->result as $program) {
            array_push($this->programs, $program);
        }

        if (count($responseBody->result) == 200) {
            if ($options) {
                $this->getPrograms(array_merge($options, ["offset" => $options["offset"] + 200]));
            } else {
                $this->getPrograms(["offset" => 200]);
            }
        }
        return $this->programs;
    }
}
?>