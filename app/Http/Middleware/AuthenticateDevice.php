<?php

namespace App\Http\Middleware;

use App\Services\DeviceAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDevice
{
    public function __construct(private DeviceAuthService $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $device = $this->auth->authenticate($token);

        if (! $device) {
            return response()->json([
                'error' => [
                    'code' => 'DEVICE_UNAUTHENTICATED',
                    'message' => 'A valid device bearer token is required.',
                    'details' => [],
                    'request_id' => $request->attributes->get('request_id'),
                ],
            ], 401);
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }
}
