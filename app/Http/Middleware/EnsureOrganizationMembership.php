<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationMembership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $organization = $user?->currentOrganization();

        if (! $organization) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'code' => 'NO_ORGANIZATION',
                        'message' => 'Your account is not an active member of an organization.',
                        'details' => [],
                        'request_id' => $request->attributes->get('request_id'),
                    ],
                ], 403);
            }

            abort(403, 'Your account is not an active member of an organization.');
        }

        $request->attributes->set('organization', $organization);
        View::share('currentOrganization', $organization);

        return $next($request);
    }
}
