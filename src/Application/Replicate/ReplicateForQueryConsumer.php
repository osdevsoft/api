<?php

namespace Osds\Api\Application\Replicate;

use Osds\Api\Application\BaseConsumer;
use Osds\Api\Domain\Bus\Command\CommandBus;

class ReplicateForQueryConsumer extends BaseConsumer
{

    private $commandBus;

    public function __construct(
        CommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function execute($message)
    {
        try {
            $originCommand = unserialize($message->getBody());
            $this->log('replicating ' . $originCommand->uuid());

            $command = new ReplicateForQueryCommand(
                $originCommand->entity(),
                $originCommand->uuid(),
                $originCommand->data(),
                get_class($originCommand)
            );
            $this->commandBus->dispatch($command, true);
        } catch (\Exception $e) {
            $this->log($e->getFile() . '::' . $e->getLine() . ' : ' . $e->getMessage());
        }
    }
}
