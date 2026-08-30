<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\DayName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreMarketingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true && $this->user()?->is_active === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::of($this->scalarInput('name'))->squish()->toString(),
            'username' => Str::of($this->scalarInput('username'))->trim()->lower()->toString(),
            'email' => Str::of($this->scalarInput('email'))->trim()->lower()->toString() ?: null,
            'code' => Str::of($this->scalarInput('code'))->trim()->upper()->toString(),
            'phone' => Str::of($this->scalarInput('phone'))->trim()->toString() ?: null,
            'area' => Str::of($this->scalarInput('area'))->squish()->toString(),
        ]);
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9._-]+$/', Rule::unique('users', 'username')],
            'email' => ['nullable', 'string', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'code' => ['required', 'string', 'max:10', Rule::unique('marketing_profiles', 'code')],
            'phone' => ['nullable', 'string', 'max:30'],
            'area' => ['required', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('mms.uploads.max_image_kb', 2048)],
            'work_days' => ['required', 'array', 'min:1'],
            'work_days.*' => ['required', 'string', 'distinct', Rule::in(DayName::operationalValues())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'username' => 'username',
            'email' => 'email',
            'password' => 'password',
            'code' => 'kode',
            'phone' => 'nomor HP',
            'area' => 'area',
            'profile_photo' => 'foto profil',
            'work_days' => 'hari kerja',
        ];
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
