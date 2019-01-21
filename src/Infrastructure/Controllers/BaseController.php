<?php

namespace Osds\Api\Infrastructure\Controllers;

use Illuminate\Http\Request;

abstract class BaseController
{

    use ControllerTrait;

    private $tokens = ['PublicTokenForRequestingAPI'];
    public $request_parameters;

    public function __construct(Request $request) {

        $api_token = @$request->header('X-Auth-Token');
        if (
            is_null($api_token)
            || !in_array($api_token, $this->tokens)
        ) {
//            die('Invalid token');
        }
        #TODO: use request variables
//        $this->request->get = $request->query->all();
//        $this->request->post = $request->request->all();
//        $this->request->files = $request->files->all();

        $this->request = new \stdClass();
        $this->request->parameters = (isset($_REQUEST))?$_REQUEST:null;

        if (!empty($_FILES)) {
            $this->request->files = $_FILES;
        }
    }

    /**
     * @param $action           name of the function that is called (same as the command first part ($actionModelCommand)
     * @param $uri_params
     * @return array            response of the command
     */
    public function __call($action, $uri_params)
    {
        $this->request->custom_parameters = new \stdClass();

        #first param of url is always the entity
        $this->request->custom_parameters->entity = array_shift($uri_params);
        #second param of the url might be the id
        $this->request->custom_parameters->entity_id = array_shift($uri_params);
        // we store the extra params of the url
//        $this->args = array_merge($this->args, ['uri_params' => $uri_params]);
        #call the command
        return $this->callAction($action);
    }


    public function __destruct()
    {
        if(class_exists('\App\Events\PostExecution'))
        {
            $middleware = new \App\Events\PostExecution();

            if(isset($_REQUEST['log_event'])) {

                if(isset($this->response['upsert_id']))
                {
                    $id = $this->response['upsert_id'];
                } else if(isset($this->entity_id)) {
                    $id = $this->entity_id;
                } else {
                    $id = 0;
                }

                if(isset($_REQUEST['user_id']))
                {
                    $user_id = $_REQUEST['user_id'];
                } else {
                    $user_id = 1;
                }

                unset($this->parameters['log_event']);

                $middleware->logEvent($_REQUEST['log_event'], $this->entity, $id, $this->parameters, $user_id);
            }

        }
    }




}