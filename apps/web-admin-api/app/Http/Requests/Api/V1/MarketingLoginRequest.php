<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class MarketingLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => Str::of($this->scalarInput('username'))->trim()->lower()->toString(),
            'device_name' => Str::of($this->scalarInput('device_name'))->squish()->toString(),
        ]);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'username' => 'username',
            'password' => 'password',
            'device_name' => 'nama perangkat',
        ];
    }

    public function authenticateMarketing(): User
    {
        $this->ensureIsNotRateLimited();

        $user = User::query()
            ->with('marketingProfile.workDays')
            ->where('username', $this->string('username')->toString())
            ->first();

        if (
            ! $user
            || $user->role !== UserRole::Marketing
            || ! $user->is_active
            || $user->marketingProfile === null
            || ! Hash::check($this->string('password')->toString(), $user->password)
        ) {
            RateLimiter::hit($this->throttleKey(), $this->decaySeconds());

            throw new HttpResponseException(ApiResponse::error(
                message: 'Username atau password tidak sesuai.',
                status: 401,
            ));
        }

        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts())) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw new HttpResponseException(ApiResponse::error(
            message: trans_choice(
                'Terlalu banyak percobaan masuk. Coba lagi dalam :seconds detik.',
                $seconds,
                ['seconds' => $seconds],
            ),
            status: 429,
        ));
    }

    public function throttleKey(): string
    {
        return Str::transliterate($this->string('username')->toString().'|'.$this->ip());
    }

    public function maxAttempts(): int
    {
        return max(1, (int) config('mms.auth.marketing_api_login_max_attempts', 5));
    }

    public function decaySeconds(): int
    {
        return max(1, (int) config('mms.auth.marketing_api_login_decay_seconds', 60));
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(ApiResponse::validationError(
            errors: $validator->errors()->toArray(),
        ));
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
