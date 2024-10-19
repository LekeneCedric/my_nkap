<?php

namespace App\Operation\Domain\VO;

class MakeAIOperationServiceResponseVO
{
    private int $consumedToken;
    private bool $operationIsOk;
    private array $operations;

    /**
     * @param array $operations
     * @param bool $operationIsOk
     * @param int $consumedToken
     */
    public function __construct( array $operations, bool $operationIsOk, int $consumedToken)
    {
        $this->operations = $operations;
        $this->operationIsOk = $operationIsOk;
        $this->consumedToken = $consumedToken;
    }

    /**
     * @return int
     */
    public function consumedToken(): int
    {
        return $this->consumedToken;
    }

    /**
     * @return bool
     */
    public function operationIsOk(): bool
    {
        return $this->operationIsOk;
    }

    /**
     * @return array
     */
    public function operations(): array
    {
        return $this->operations;
    }
}
