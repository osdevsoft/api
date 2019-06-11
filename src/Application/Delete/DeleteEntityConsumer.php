<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Application\BaseConsumer;

class DeleteEntityConsumer extends BaseConsumer
{

    private $command;

    public function __construct(
        DeleteEntityCommandHandler $commandHandler
    ) {
        $this->command = $commandHandler;
    }

    public function execute($message)
    {
        try {
            $originCommand = unserialize($message->getBody());
            $this->log('deleting ' . $originCommand->uuid());

            $this->command->handle($originCommand, true);

        } catch (\Exception $e) {
            $this->log('there was an error during the deletion: ' . $message->getBody());
        }
    }
}
