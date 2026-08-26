<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

class GenerateOperationalRecapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true && $this->user()?->is_active === true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'recap_date' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', (string) $this->input('recap_date'));

            if ($date && $date->isSunday()) {
                $validator->errors()->add('recap_date', 'Rekap hanya dapat dibuat pada hari operasional.');
            }
        });
    }
}
