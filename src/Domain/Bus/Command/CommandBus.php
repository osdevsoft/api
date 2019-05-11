<?php

namespace Osds\Api\Domain\Bus\Command;

use Osds\Api\Infrastructure\Bus\ContainerInterface;

class CommandBus implements CommandBusInterface
{
    private $container;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container->handler();
    }

    public function dispatch(Command $messageObject, $forceExecution = false)
    {

        $queryHandler = $this->getCommandHandler($messageObject);
        return $queryHandler->handle($messageObject, $forceExecution);

    }

    private function getCommandHandler($messageObject)
    {
        try {
            $queryHandlerClass = get_class($messageObject) . 'Handler';
            return $this->container->get($queryHandlerClass);
        } catch(\Exception $e) {
            dd($e->getMessage());
        }

    }
}