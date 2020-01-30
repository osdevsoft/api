<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\Messaging\MessagingInterface;

final class InsertEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $messaging;

    public function __construct(
        InsertEntityUseCase $useCase,
        MessagingInterface $messaging
    ) {
        $this->useCase = $useCase;
        $this->messaging = $messaging;
    }

    public function handle(InsertEntityCommand $command, $forceExecution = false)
    {
        $queue = $command->getQueue();
        if (!$forceExecution
            && ($queue !== null)
        ) {
             $this->messaging->publish($queue, $command->getPayload());
        } else {
            $this->useCase->execute(
                $command->entity(),
                $command->uuid(),
                $command->data()
            );
            #$this->messaging->publish('insert_completed', $command->getPayload());
        }

         return $command->uuid();
    }
}
