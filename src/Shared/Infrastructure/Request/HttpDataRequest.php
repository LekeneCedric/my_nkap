<?php

namespace App\Shared\Infrastructure\Request;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class HttpDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
          response()->json([
              'status' => false,
              'message' => $validator->errors()->first()
          ]),
          200
        );
    }
}
