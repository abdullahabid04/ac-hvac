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
        // Try bearer token authentication first
        $token = $request->bearerToken();
        $device = $this->auth->authenticate($token);

        if ($device) {
            $request->attributes->set('device', $device);
            return $next($request);
        }

        // Fall back to serial-number-based authentication (temporary/demo only)
        $serialNumber = $request->header('X-Device-Serial');
        if ($serialNumber) {
            $device = $this->auth->authenticateBySerialNumber($serialNumber);
            if ($device) {
                $request->attributes->set('device', $device);
                return $next($request);
            }
        }

        return response()->json([
            'error' => [
                'code' => 'DEVICE_UNAUTHENTICATED',
                'message' => 'A valid device bearer token or device serial is required.',
                'details' => [],
                'request_id' => $request->attributes->get('request_id'),
            ],
        ], 401);
    }
}
