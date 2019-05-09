<?php

namespace Osds\Api\Domain\Bus\Query;

use Osds\Api\Infrastructure\Bus\ContainerInterface;

class QueryBus implements QueryBusInterface
{
    private $container;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container->handler();
    }

    public function ask(Query $messageObject) {

        $queryHandler = $this->getQueryHandler($messageObject);
        return $queryHandler->handle($messageObject);

    }

    private function getQueryHandler($messageObject) {

        try {
            $queryHandlerClass = get_class($messageObject) . 'Handler';
            return $this->container->get($queryHandlerClass);
        } catch(\Exception $e) {
            dd($e);
        }

    }
}