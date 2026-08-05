<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => Str::of($this->scalarInput('username'))->trim()->lower()->toString(),
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
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'username' => $this->string('username')->toString(),
            'password' => $this->string('password')->toString(),
            'role' => UserRole::Admin->value,
            'is_active' => true,
        ];

        if (! Auth::guard('web')->attempt($credentials)) {
            RateLimiter::hit($this->throttleKey(), $this->decaySeconds());

            throw ValidationException::withMessages([
                'username' => 'Username atau password tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts())) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans_choice(
                'Terlalu banyak percobaan masuk. Coba lagi dalam :seconds detik.',
                $seconds,
                ['seconds' => $seconds],
            ),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate($this->string('username')->toString().'|'.$this->ip());
    }

    public function maxAttempts(): int
    {
        return max(1, (int) config('mms.auth.admin_login_max_attempts', 5));
    }

    public function decaySeconds(): int
    {
        return max(1, (int) config('mms.auth.admin_login_decay_seconds', 60));
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
