<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Application\BaseConsumer;

class InsertEntityConsumer extends BaseConsumer
{

    private $command;

    public function __construct(
        InsertEntityCommandHandler $commandHandler
    )
    {
        $this->command = $commandHandler;
    }

    public function execute($message)
    {
        try {

            $command = unserialize($message->getBody());
            $this->log('inserting ' . $command->uuid());
            $this->command->handle($command, true);

        } catch(\Exception $e) {
            $this->log('there was an error during the insertion: ' . $message->getBody());
        }


    }

}