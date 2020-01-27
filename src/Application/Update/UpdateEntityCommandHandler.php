<?php

namespace Osds\Api\Application\Update;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\Messaging\MessagingInterface;

final class UpdateEntityCommandHandler implements CommandHandler
{
    private $useCase;
    private $messaging;

    public function __construct(
        UpdateEntityUseCase $useCase,
        MessagingInterface $messaging
    ) {
        $this->useCase = $useCase;
        $this->messaging = $messaging;
    }

    public function handle(UpdateEntityCommand $command, $forceExecution = false)
    {
        try {
            $queue = $command->getQueue();

            if (!$forceExecution
                && ($queue !== null)
            ) {
                 $this->messaging->publish($queue, $command->getPayload());
                 return 'published';
                 
            } else {
                $update = $this->useCase->execute(
                    $command->entity(),
                    $command->uuid(),
                    $command->data()
                );
//                $this->messaging->publish('update_completed', $command->getPayload());
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

         return $command->uuid();
    }
}
