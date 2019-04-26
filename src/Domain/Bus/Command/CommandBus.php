<?php

namespace Osds\Api\Domain\Bus\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandBus implements CommandBusInterface
{
    private $container;

    public function __construct(
        ContainerInterface
        $container
    ) {
        $this->container = $container;
    }

    public function dispatch(Command $messageObject) {

        $queryHandler = $this->getCommandHandler($messageObject);
        return $queryHandler->handle($messageObject);

    }

    private function getCommandHandler($messageObject) {

        try {
            $queryHandlerClass = get_class($messageObject) . 'Handler';
            return $this->container->get($queryHandlerClass);
        } catch(\Exception $e) {
            dd($e);
        }

    }
}