<?php

namespace App\Statistics\Application\Query\MonthlyStatistics\All;

use App\Statistics\Domain\repositories\MonthlyStatisticRepository;

class GetAllMonthlyStatisticsHandler
{
    public function __construct(
        private MonthlyStatisticRepository $repository,
    )
    {
    }

    public function handle(GetAllMonthlyStatisticsCommand $command): GetAllMonthlyStatisticResponse
    {
        $response = new GetAllMonthlyStatisticResponse();
        $data = $this->repository->ofFilterParams(userId: $command->userId, year: $command->year, month: $command->month);
        $response->data = $data;
        return $response;
    }
}
