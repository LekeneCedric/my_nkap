<?php

namespace App\Operation\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Request\HttpDataRequest;

class MakeOperationRequest extends HttpDataRequest
{
    public function rules(): array
    {
        return [
            'accountId' => 'required|string',
            'type' => 'required|integer',
            'amount' => 'required|decimal:0,4',
            'category' => 'required|string',
            'detail' => 'required|string',
            'date' => 'required|string',
        ];
    }
}
