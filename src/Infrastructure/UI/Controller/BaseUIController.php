<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Osds\Auth\Domain\Exception\InvalidTokenException;
use Osds\Auth\Infrastructure\UI\ServiceAuth;

use Symfony\Component\HttpFoundation\JsonResponse;

class BaseUIController
{
    protected $tokenValidation;
    private $newServiceToken = null;

    public function build() {

        $this->tokenValidation = $this->checkServiceAuth();

        $requestParameters = [];
//        $postData = file_get_contents("php://input");
        $postData = null;
        if (!empty($postData)) {
            $requestParameters['post'] = http_build_query(json_decode($postData, true));
        } else {
            $requestParameters['all'] = $_REQUEST;
            $requestParameters['post'] = $_POST;
            $requestParameters['get'] = $_GET;
        }

        if (!empty($_FILES)) {
            $requestParameters['files'] = $_FILES;
        }

        return $requestParameters;
    }

    public function checkServiceAuth()
    {
        try {
            if(!strstr(get_called_class(), 'ServiceAuthController')) {
                $token = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:null;
                $serviceTokenCheck = ServiceAuth::checkServiceToken($token);
                if (is_string($serviceTokenCheck)) {
                    #new token provided => we have to return it
                    $this->newServiceToken = $serviceTokenCheck;
                }
                return true;
            }
        } catch(InvalidTokenException $e) {
            $e->setLogger($this->logger);
            $e->setMessage('Invalid Service Token', $e);
            return $this->generateResponse($e->getResponse());
        }

    }

    public function generateResponse($data)
    {
        if($this->newServiceToken !== null) {
            $data['renewedServiceToken'] = $this->newServiceToken;
        }
        return new JsonResponse($data, 200);
    }

    public function prepareResponseByItems($data)
    {
        $items[] = $data;
        $num_items = ($data != true)? 0: count($items);

        return [
            'total_items' => $num_items,
            'items' => $items
        ];
    }
}
