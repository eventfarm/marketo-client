GETPROGRAMS()
  - getPrograms takes an optional array of options.

  - Possible responses from Marketo:

    - If no programs exist (at all, or that match the options you pass in), Marketo will send a warning:
      {
        ["success"]=>
          bool(true)
        ["warnings"]=>
          array(1) {
            [0]=>
              string(46) "No assets found for the given search criteria."
          }
        ["errors"]=>
          array(0) {
          }
        ["requestId"]=>
          string(17) "xxxxxxxxxxxxxxxxx"
      }

    - If there is an error with the request, Marketo will send errors:
      {
        ["success"]=>
          bool(false)
        ["warnings"]=>
          array(0) {
          }
        ["errors"]=>
          array(1) {
            [0]=>
              object(stdClass)#42 (2) {
                ["message"]=>
                  string(36) "maxReturn cannot be greater than 200"
                ["code"]=>
                  string(3) "701"
              }
          }
        ["requestId"]=>
          string(17) "xxxxxxxxxxxxxxxxx"
      }

    - Marketo sends us programs in batches of 200. If you don't get any errors or warnings, you might want to execute something like this to check the number of programs, and then request another batch:
      // 
        if (count($responseBody->result) == 200) {
            foreach ($responseBody->result as $program) {
                array_push($this->programs, $program);
            }
            if ($options) {
                $this->getPrograms(array_merge($options, ["offset" => $options["offset"] + 200]));
            } else {
                $this->getPrograms(["offset" => 200]);
            }
        }