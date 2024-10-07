<?php

namespace App\Traits\Helpers;
use Illuminate\Http\RedirectResponse;

trait FallbackResolver
{
    private function resolveFallback(bool $needToResolve): RedirectResponse|bool|null
    {
        if ($needToResolve) {
            if (url()->previous() !== url()->current()) {
                return redirect()->back()->withInput();
            } else {
                abort(404);
                return true;
            }
        }

        return null;
    }
}
