<?php

namespace App\Statistics\Infrastructure\ViewModels;

use App\Statistics\Application\Query\MonthlyStatistics\All\GetAllMonthlyStatisticResponse;

class GetAllMonthlyStatisticViewModel
{
    public function __construct(
        private int $selectedMonth,
        private GetAllMonthlyStatisticResponse $response,
    )
    {
    }

    public function toArray(): array
    {
        $expense = $this->extractExpense();
        $incomes = $this->extractIncomes();

        return [
            'incomes' => $incomes,
            'expenses' => $expense,
        ];
    }

    private function extractExpense(): array
    {
        $result = ['months' => []];
        $totalExpensePreviousMonth = 0;
        $totalExpenseCurrentMonth = 0;
        foreach ($this->response->data as $data) {
            if ($data['month'] == $this->selectedMonth) {
                $totalExpenseCurrentMonth = $data['total_expense'];
            }
            if (
                ($data['month'] == 12 && $this->selectedMonth == 1) ||
                ($data['month'] == $this->selectedMonth - 1)
            ) {
                $totalExpensePreviousMonth = $data['total_expense'];
            }
            $result['months'][] = [
                'month' => $data['month'],
                'totalExpenses' => $data['total_expense']
            ];

        }
        $result['difference'] = $totalExpenseCurrentMonth - $totalExpensePreviousMonth;
        return $result;
    }

    private function extractIncomes(): array
    {
        $result = ['months' => []];
        $totalIncomePreviousMonth = 0;
        $totalIncomeCurrentMonth = 0;
        foreach ($this->response->data as $data) {
            if ($data['month'] == $this->selectedMonth) {
                $totalIncomeCurrentMonth = $data['total_income'];
            }
            if (
                ($data['month'] == 12 && $this->selectedMonth == 1) ||
                ($data['month'] == $this->selectedMonth - 1)
            ) {
                $totalIncomePreviousMonth = $data['total_income'];
            }
            $result['months'][] = [
                'month' => $data['month'],
                'totalIncomes' => $data['total_income']
            ];

        }
        $result['difference'] = $totalIncomeCurrentMonth - $totalIncomePreviousMonth;
        return $result;
    }
}
