<?php

namespace App\Operation\Infrastructure\ViewModels;

use App\Operation\Application\Queries\Filter\FilterAccountOperationsResponse;
use App\Operation\Domain\OperationTypeEnum;

class FilterAccountOperationsViewModel
{
    public function __construct(
        private FilterAccountOperationsResponse $response,
    )
    {
    }

    public function toArray(): array
    {
        $operations = $this->response->operations;
        return $this->groupOperationsByDate($operations);
    }

    private function groupOperationsByDate(array $operations): array
    {
        $result = [];
        foreach ($operations as $operation)
        {
            $date = date('Y-m-d', strtotime($operation['date']));

            if (!isset($result[$date])) {
                $result[$date] = [];
                $result[$date]['totalExpense'] = 0;
                $result[$date]['totalIncomes'] = 0;
                $result[$date]['date'] = $date;
            }
            if ($operation['type'] === OperationTypeEnum::EXPENSE->value) {
                $result[$date]['totalExpense']+= $operation['amount'];
            }
            if ($operation['type'] === OperationTypeEnum::INCOME->value) {
                $result[$date]['totalIncomes']+= $operation['amount'];
            }
            $result[$date]['operations'][] = $operation;
        }
        $results = [];
        foreach ($result as $res) {
            $results[] = $res;
        }
        return $results;
    }
}
