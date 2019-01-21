<?php

namespace Osds\Api\Infrastructure\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ControllerTrait
{

    private $commands_ns = '\Osds\Api\Application\%action%\%action%EntityUseCase';

    public function callAction($action)
    {
        try
        {
            #get command location based on the action required
            $command_location = $this->getUseCaseLocation($action);

            #execute loaded command
            $command = new $command_location();
            $command->setBaseSettings($this->request->custom_parameters->entity, $this->request);
            $res = $command->execute();
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
    private function getUseCaseLocation($action)
    {
        #does this entityhave a custom command on the project src?
        $model_command = 'App\Application\%action%\\' . ucfirst($this->request->custom_parameters->entity) . '\\' . ucfirst($action) . ucfirst($this->request->custom_parameters->entity) . 'Command';
        if(class_exists($model_command))
        {
            return $model_command;
        }

        #return generic command
        return str_replace(
            '%action%',
            ucfirst($action),
            $this->commands_ns
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