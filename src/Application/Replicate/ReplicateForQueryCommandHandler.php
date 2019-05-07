<?php

namespace Osds\Api\Application\Replicate;

use Osds\Api\Domain\Bus\Command\CommandHandler;
use Osds\Api\Infrastructure\AMQP\AMQPInterface;

final class ReplicateForQueryCommandHandler implements CommandHandler
{
    private $useCase;

    public function __construct(
        ReplicateforQueryUseCase $useCase
    )
    {
        $this->useCase = $useCase;
    }

    public function handle(ReplicateForQueryCommand $command, $forceExecution = false)
    {
        try {

            $this->useCase->execute(
                $command->entity(),
                $command->uuid(),
                $command->data()
            );

        } catch(\Exception $e) {
            dd($e->getMessage());
        }

         return $command->uuid();
    }
}
