<?php

namespace App\Statistics\Application\Query\MonthlyCategoryStatistics\All;

use App\Statistics\Domain\repositories\MonthlyCategoryStatisticRepository;

class GetAllMonthlyCategoryStatisticsHandler
{
    public function __construct(
        private MonthlyCategoryStatisticRepository $repository,
    )
    {
    }

    public function handle(GetAllMonthlyCategoryStatisticsCommand $command): GetAllMonthlyCategoryStatisticsResponse
    {
        $response = new GetAllMonthlyCategoryStatisticsResponse();
        $data = $this->repository->ofFilterParams(
            userId: $command->userId,
            year: $command->year,
            month: $command->month
        );
        $response->data = $data;
        return $response;
    }
}
