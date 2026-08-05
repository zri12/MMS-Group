<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

final class ApiRequest
{
    public static function shouldReturnJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}
