<?php

namespace App\Operation\Domain;

enum OperationTypeEnum: int
{
    case INCOME = 1;
    case EXPENSE = 2;
}
