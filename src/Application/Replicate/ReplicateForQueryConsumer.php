<?php

namespace Osds\Api\Application\Replicate;

use Osds\Api\Application\BaseConsumer;
use Osds\Api\Domain\Bus\Command\CommandBus;
use Osds\Api\Infrastructure\Log\LoggerInterface;

class ReplicateForQueryConsumer extends BaseConsumer
{

    private $commandBus;
    private $logger;

    public function __construct(
        CommandBus $commandBus,
        LoggerInterface $logger
    ) {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public function execute($message)
    {
        try {
            $originCommand = unserialize($message->getBody());
            $this->logger->info('replicating ' . $originCommand->uuid());

            $command = new ReplicateForQueryCommand(
                $originCommand->entity(),
                $originCommand->uuid(),
                $originCommand->data(),
                get_class($originCommand)
            );
            $this->commandBus->dispatch($command, true);
        } catch (\Exception $e) {
            $this->logger->error($e->getFile() . '::' . $e->getLine() . ' : ' . $e->getMessage());
        }
    }
}
