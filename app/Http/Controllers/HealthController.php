<?php

namespace App\Http\Controllers;

use App\Support\Health\HealthChecker;
use Illuminate\Http\JsonResponse;

final class HealthController extends Controller
{
    public function __invoke(HealthChecker $checker): JsonResponse
    {
        $payload = $checker->check();
        $ok = $payload['status'] === 'ok';

        return response()->json($payload, $ok ? 200 : 503);
    }
}
