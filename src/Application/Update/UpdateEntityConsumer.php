<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Application\BaseConsumer;

class UpdateEntityConsumer extends BaseConsumer
{

    private $command;

    public function __construct(
        UpdateEntityCommandHandler $commandHandler
    ) {
        $this->command = $commandHandler;
    }

    public function execute($message)
    {
        try {
            $originCommand = unserialize($message->getBody());
            $this->log('updating ' . $originCommand->uuid());

            $this->command->handle($originCommand, true);

        } catch (\Exception $e) {
            $this->log('there was an error during the update: ' . $message->getBody());
        }
    }
}
