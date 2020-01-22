<?php

namespace Osds\Api\Infrastructure\UI\Controller;

use Illuminate\Http\Request;
use Osds\Api\Infrastructure\Persistence\SessionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;


class BaseUIController
{
    protected $request;

    public function build(
        Request $request
    ) {
        $this->request = new \stdClass();
        $postData = file_get_contents("php://input");

        if (!empty($postData)) {
            $this->request->parameters = json_decode($postData, true);
        } else {
            $this->request->parameters = $_REQUEST;
        }

        $session = new SessionRepository();
        $session->insert('request_parameters', $this->request->parameters);

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
