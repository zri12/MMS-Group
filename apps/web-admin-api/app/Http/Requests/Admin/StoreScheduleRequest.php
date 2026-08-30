<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\DayName;
use App\Enums\ScheduleStatus;
use App\Models\Prospect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true && $this->user()?->is_active === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'consumer_name_snapshot' => Str::of($this->scalarInput('consumer_name_snapshot'))->squish()->toString() ?: null,
            'agenda' => Str::of($this->scalarInput('agenda'))->squish()->toString(),
            'area' => Str::of($this->scalarInput('area'))->squish()->toString(),
            'resort' => Str::of($this->scalarInput('resort'))->squish()->toString() ?: null,
            'destination' => Str::of($this->scalarInput('destination'))->squish()->toString() ?: null,
            'note' => Str::of($this->scalarInput('note'))->trim()->toString() ?: null,
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'marketing_profile_id' => ['required', 'integer', 'exists:marketing_profiles,id'],
            'prospect_id' => ['nullable', 'integer', Rule::exists('prospects', 'id')],
            'day_name' => ['required', 'string', Rule::in(DayName::operationalValues())],
            'schedule_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'consumer_name_snapshot' => ['nullable', 'string', 'max:150'],
            'agenda' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:100'],
            'resort' => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', Rule::in(ScheduleStatus::values())],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $prospectId = $this->integer('prospect_id') ?: null;

            if (! $prospectId) {
                return;
            }

            $belongsToMarketing = Prospect::query()
                ->whereKey($prospectId)
                ->where('marketing_profile_id', $this->integer('marketing_profile_id'))
                ->exists();

            if (! $belongsToMarketing) {
                $validator->errors()->add('prospect_id', 'Prospek harus milik marketing yang dipilih.');
            }
        });
    }

    private function scalarInput(string $key): string
    {
        $value = $this->input($key);

        if (is_bool($value) || is_numeric($value) || is_string($value)) {
            return (string) $value;
        }

        return '';
    }
}
