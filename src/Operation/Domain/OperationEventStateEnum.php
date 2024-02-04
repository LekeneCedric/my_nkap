<?php

namespace App\Operation\Domain;

enum OperationEventStateEnum
{
    case onUpdate;
    case onCreate;
    case onDelete;
}
