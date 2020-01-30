<?php

namespace Osds\Api\Application\Delete;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\Messaging\MessagingInterface;

final class DeleteEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $messaging;

    public function __construct(
        DeleteEntityUseCase $useCase,
        MessagingInterface $messaging
    ) {
        $this->useCase = $useCase;
        $this->messaging = $messaging;
    }

    public function handle(DeleteEntityCommand $command, $forceExecution = false)
    {
        try {
            $queue = $command->getQueue();
            if (!$forceExecution
                && ($queue !== null)
            ) {
                 $this->messaging->publish($queue, $command->getPayload());
            } else {
                $this->useCase->execute(
                    $command->entity(),
                    $command->uuid()
                );
                #$this->messaging->publish('delete_completed', $command->getPayload());

            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

         return $command->uuid();
    }
}
