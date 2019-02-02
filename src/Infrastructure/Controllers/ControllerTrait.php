<?php

namespace Osds\Api\Infrastructure\Controllers;

trait ControllerTrait
{

    private $action_ns = '\Osds\Api\Application\%action%\%action%EntityAction';

    private $request;

    public function callAction($action)
    {
        try
        {
            #get action location based on the action required
            $action_location = $this->getActionLocation($action);

            #execute loaded action
            $action = new $action_location($this->services['query_bus']);

            $message_object = $action->getMessageObject($this->request->custom_parameters->entity, $this->request);
            $res = $action->call($message_object, $this->services['query_bus']);


        } catch(\Exception $e)
        {

            $res = [
                'error_message' => $e->getMessage() . ' on ' . basename($e->getFile() . '::' . $e->getLine())
            ];


        }
        return $res;
    }

    /**
     * Returns the fully qualified name of the command to execute
     *
     * @param $action
     * @return mixed
     */
    private function getActionLocation($action)
    {
        #does this entity have a custom command on the project src?
        $model_command = 'App\Application\%action%\\' . ucfirst($this->request->custom_parameters->entity) . '\\' . ucfirst($action) .
            ucfirst($this->request->custom_parameters->entity) . 'Command';
        if(class_exists($model_command))
        {
            return $model_command;
        }

        #return generic command
        return str_replace(
            '%action%',
            ucfirst($action),
            $this->action_ns
        );
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