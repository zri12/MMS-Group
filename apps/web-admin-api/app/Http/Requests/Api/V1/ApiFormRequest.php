<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(ApiResponse::validationError(
            errors: $validator->errors()->toArray(),
        ));
    }

    protected function perPage(): int
    {
        $default = (int) config('mms.pagination.default_per_page', 20);
        $max = (int) config('mms.pagination.max_per_page', 100);
        $requested = (int) $this->input('per_page', $default);

        return min(max($requested, 1), $max);
    }
}
