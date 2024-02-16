<?php

namespace App\Operation\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Request\HttpDataRequest;

class DeleteOperationRequest extends HttpDataRequest
{
    public function rules(): array
    {
        return [
          'accountId' => 'required|string',
          'operationId' => 'required|string'
        ];
    }
}
