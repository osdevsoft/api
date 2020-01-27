<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Osds\Api\Infrastructure\Persistence\SessionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class BaseUIController
{
    protected $request;

    public function build() {
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

    public function generateResponse($data)
    {
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
