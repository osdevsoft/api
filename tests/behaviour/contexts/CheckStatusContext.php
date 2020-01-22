<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;

class CheckStatusContext implements Context
{

    private $domain = '';
    private $endpoint = '';
    private $api_response;

    public function __construct()
    {
        // instantiate context
    }


    /**
     * @Given The Domain :domain is available
     */
    public function theDomainIsAvailable($domain)
    {
        $this->domain = $domain;
        $response = $this->callEndpoint($domain);
        $response_code = $response->getStatusCode();

        if ($response_code != 200) {
            throw new Exception('API not available, http error ' . $response_code);
        }

        return true;
    }

    /**
     * @When I request the status url :end_point
     */
    public function iRequestTheEndpoint($end_point)
    {
        $this->endpoint = $end_point;
        $this->api_response = $this->callEndpoint($this->domain, $end_point);
        $responseCode = $this->api_response->getStatusCode();

        if ($responseCode != 200) {
            throw new Exception($end_point . ' not available');
        }

        return true;
    }

    /**
     * @Then I should see :arg1
     */
    public function iShouldSee($result)
    {
        $response = $this->callEndpoint($this->domain, $this->endpoint);
        $responseBody = $response->getBody();
        $responseBody = json_decode($responseBody);

        if ($responseBody != $result) {
            throw new Exception('API not available');
        }
    }


    private function callEndpoint($domain, $end_point = '')
    {
        $client = new \GuzzleHttp\Client(['base_uri' => $domain]);

        try {
            $response = $client->request('get', $end_point);
        } catch (\Exception $e) {
            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                $response = $e->getResponse();
            }
        }
        return $response;
    }

}
