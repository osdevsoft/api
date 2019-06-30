<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Application\BaseConsumer;
use Osds\Api\Domain\Exception\ErrorException;
use Osds\Api\Infrastructure\Log\LoggerInterface;

class InsertEntityConsumer extends BaseConsumer
{

    private $command;
    private $logger;

    public function __construct(
        InsertEntityCommandHandler $commandHandler,
        LoggerInterface $logger
    ) {
        $this->command = $commandHandler;
        $this->logger = $logger;
    }

    public function execute($message)
    {
        try {
            $originCommand = unserialize($message->getBody());
            $this->logger->info('inserting ' . $originCommand->uuid());

            $this->command->handle($originCommand, true);

        } catch (\Exception $e) {
            $exception = new ErrorException($this->logger);
            $exception->setMessage(
                'there was an error during the insertion: ' . $message->getBody(),
                $e
            );
            return $exception->getResponse();
        }
    }
}
