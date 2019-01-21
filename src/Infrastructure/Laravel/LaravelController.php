<?php

namespace Osds\Api\Framework\Laravel;

use Osds\Api\Infrastructure\Controllers\BaseController;

class LaravelController extends BaseController {

    public function generateResponse($data) {

        return response()->json($data, 200);

    }

}