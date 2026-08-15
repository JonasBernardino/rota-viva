<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the authentication form.
     */
    public function showLogin(): View|RedirectResponse
    {
        $redirect = request()->query('redirect');

        if (is_string($redirect) && $this->isInternalRedirect($redirect)) {
            session()->put('url.intended', $redirect);
        }

        if (Auth::check()) {
            return redirect()->intended($this->authenticatedHome(request()->user()));
        }

        return view('pages.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (! (bool) ($request->user()->can_access_admin_panel ?? false)) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Este acesso é exclusivo para a gestão municipal.',
                ])->onlyInput('email');
            }

            return redirect()->intended($this->authenticatedHome($request->user()));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function isInternalRedirect(string $redirect): bool
    {
        $host = parse_url($redirect, PHP_URL_HOST);

        if ($host === null) {
            return str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//');
        }

        return $host === request()->getHost();
    }

    private function authenticatedHome(mixed $user): string
    {
        if ((bool) ($user->can_access_admin_panel ?? false)) {
            return route('admin.dashboard');
        }

        return route('home');
    }
}
