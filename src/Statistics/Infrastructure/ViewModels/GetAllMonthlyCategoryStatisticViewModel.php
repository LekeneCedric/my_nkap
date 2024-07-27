<?php

namespace App\Statistics\Infrastructure\ViewModels;

use App\Statistics\Application\Query\MonthlyCategoryStatistics\All\GetAllMonthlyCategoryStatisticsResponse;

class GetAllMonthlyCategoryStatisticViewModel
{
    public function __construct(
        public int                                     $selectedMonth,
        public GetAllMonthlyCategoryStatisticsResponse $response,
    )
    {
    }

    public function toArray(): array
    {
        $incomes = $this->extractIncomes();
        $expenses = $this->extractExpenses();

        return [
            'incomes' => $incomes,
            'expenses' => $expenses,
        ];
    }

    private function extractIncomes(): array
    {
        $result = [];
        $totalIncomes = array_reduce($this->response->data, fn($carry, $item) => $carry + $item['total_income'], 0);
        foreach ($this->response->data as $data) {
            $result[] = [
                'id' => $data['id'],
                'composedId' => $data['composed_id'],
                'userId' => $data['user_id'],
                'year' => $data['year'],
                'month' => $data['month'],
                'categoryId' => $data['category_id'],
                'categoryIcon' => $data['category_icon'],
                'categoryLabel' => $data['category_label'],
                'categoryColor' => $data['category_color'],
                'totalIncome' => $data['total_income'],
                'percentage' => round(($data['total_income'] * 100)/$totalIncomes),
            ];
        }
        return $result;
    }

    private function extractExpenses(): array
    {
        $result = [];
        $totalExpenses = array_reduce($this->response->data, fn($carry, $item) => $carry + $item['total_expense'], 0);
        foreach ($this->response->data as $data) {
            $result[] = [
                'id' => $data['id'],
                'composedId' => $data['composed_id'],
                'userId' => $data['user_id'],
                'year' => $data['year'],
                'month' => $data['month'],
                'categoryId' => $data['category_id'],
                'categoryIcon' => $data['category_icon'],
                'categoryLabel' => $data['category_label'],
                'categoryColor' => $data['category_color'],
                'totalExpense' => $data['total_expense'],
                'percentage' => round(($data['total_expense'] * 100)/$totalExpenses),
            ];
        }
        return $result;
    }
}
