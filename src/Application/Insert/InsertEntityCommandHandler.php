<?php

namespace Osds\Api\Application\Insert;

use Osds\Api\Domain\Bus\Command\CommandHandler;

final class InsertEntityCommandHandler implements CommandHandler
{
    private $use_case;

    public function __construct(InsertEntityUseCase $use_case)
    {
        $this->use_case = $use_case;
    }

    public function handle(InsertEntityCommand $command)
    {
        return $this->use_case->execute(
            $command->entity(),
            $command->id(),
            $command->data()
            );
    }
}
