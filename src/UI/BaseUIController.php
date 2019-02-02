<?php

namespace Osds\Api\UI;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property array services
 * @property \stdClass request
 */
class BaseUIController
{
    public function build(
        Request $request
    )
    {
        $this->services = [];
        $_SESSION['services'] = $this->services;

        $api_token = @$request->header('X-Auth-Token');

        $this->request = new \stdClass();
        $this->request->parameters = (isset($_REQUEST))?$_REQUEST:null;

        if (!empty($_FILES)) {
            $this->request->files = $_FILES;
        }
    }

    public function generateResponse($data, $action = null)
    {

        #assigned to have it on the destruct()
        $this->response = $data;
        // All actions except 'get' do not return 'items schema' => Generate it!!
        if($action != 'get'){
            return JsonResponse::fromJsonString(json_encode($this->prepareResponseByItems($data)));
        } else {
            return JsonResponse::fromJsonString(json_encode($data));
        }
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