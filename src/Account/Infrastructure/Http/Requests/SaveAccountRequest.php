<?php

namespace App\Account\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Request\HttpDataRequest;
class SaveAccountRequest extends HttpDataRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => 'required|string',
            'icon' => 'required|string',
            'color' => 'required|string',
            'balance' => 'required|decimal:0,4',
            'isIncludeInTotalBalance' => 'required|boolean'
        ];
    }
}
