<?php

namespace App\Operation\Application\Command\MakeManyOperations;

use App\Operation\Application\Command\MakeOperation\MakeOperationCommand;
use App\Shared\Domain\Command\Command;

class MakeManyOperationsCommand extends Command
{
    /**
     * @var MakeOperationCommand[]
     */
    public array $operations = [];
}
