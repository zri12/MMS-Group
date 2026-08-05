<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->role === UserRole::Admin && $user->is_active) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('login');
    }
}
