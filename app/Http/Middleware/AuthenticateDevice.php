<?php

namespace App\Http\Middleware;

use App\Services\DeviceAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDevice
{
    /**
     * Serial number format established by DeviceSerialService:
     * HVAC-ESP32-YYYY-NNNNNN (e.g. HVAC-ESP32-2026-000001)
     */
    private const SERIAL_PATTERN = '/^HVAC-ESP32-\d{4}-\d{6}$/';

    public function __construct(private DeviceAuthService $auth) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Serial-number-only device authentication.
        // Token-based (bearer/Authorization header) device authentication is
        // intentionally NOT required in this phase.

        // The serial number may be sent as a header (works for all HTTP methods)
        // or as a body field (for POST/PUT requests with a JSON body).
        $serialNumber = $request->header('X-Device-Serial');

        if (! $serialNumber && $request->has('device_serial_number')) {
            $serialNumber = $request->input('device_serial_number');
        }

        // Missing serial number
        if (! $serialNumber || ! is_string($serialNumber) || trim($serialNumber) === '') {
            return $this->unauthorized('DEVICE_SERIAL_REQUIRED', 'A device serial number is required.');
        }

        $serialNumber = trim($serialNumber);

        // Invalid serial number format
        if (! preg_match(self::SERIAL_PATTERN, $serialNumber)) {
            return $this->unauthorized('DEVICE_SERIAL_INVALID', 'The provided device serial number is invalid.');
        }

        // Unknown serial number / inactive device
        $device = $this->auth->authenticateBySerialNumber($serialNumber);

        if (! $device) {
            return $this->unauthorized('DEVICE_NOT_FOUND', 'The provided device serial number is not registered or the device is not active.');
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }

    private function unauthorized(string $code, string $message): Response
    {
        return response()->json([
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => [],
                'request_id' => request()->attributes->get('request_id'),
            ],
        ], 401);
    }
}
