<?php

namespace Osds\Api\UI\Controller;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class BaseUIController
{
    public function build(
        Request $request
    )
    {
        $api_token = @$request->header('X-Auth-Token');

        $this->request = new \stdClass();
        $postdata = file_get_contents("php://input");
        if(!empty($postdata)) {
            $this->request->parameters = json_decode($postdata, true);
        }
        else {
            $this->request->parameters = $_REQUEST;
        }

        if (!empty($_FILES)) {
            $this->request->files = $_FILES;
        }
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