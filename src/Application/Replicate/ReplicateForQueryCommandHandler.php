<?php

namespace Osds\Api\Application\Replicate;

use Osds\Api\Domain\Bus\Command\CommandHandler;

final class ReplicateForQueryCommandHandler implements CommandHandler
{
    private $useCaseFactory;

    public function __construct(
        ReplicateForQueryUseCaseFactory $useCaseFactory
    ) {
        $this->useCaseFactory = $useCaseFactory;
    }

    public function handle($command)
    {
        $useCase = $this->useCaseFactory->build($command);

        $useCase->execute(
            $command->entity(),
            $command->uuid(),
            $command->data()
        );

        return $command->uuid();
    }
}
