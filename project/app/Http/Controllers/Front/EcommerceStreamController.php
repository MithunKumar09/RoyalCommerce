<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\CommerceState;
use Illuminate\Http\Request;

class EcommerceStreamController extends Controller
{
    public function stream(Request $request)
    {
        $response = response()->stream(function () use ($request) {
            @set_time_limit(0);

            // Best-effort: disable buffering/compression so events flush promptly.
            @ini_set('zlib.output_compression', '0');
            @ini_set('output_buffering', '0');
            @ini_set('implicit_flush', '1');

            // Try to disable output buffering.
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ob_implicit_flush(true);

            // Some proxies only start streaming after initial output.
            echo ":ok\n\n";
            @ob_flush();
            @flush();

            $send = function (string $event, array $data, ?int $id = null): void {
                if ($id !== null) {
                    echo "id: {$id}\n";
                }
                echo "event: {$event}\n";
                echo 'data: ' . json_encode($data) . "\n\n";
                @ob_flush();
                @flush();
            };

            // Initial payload
            $state = CommerceState::current();
            $lastVersion = (int) ($request->header('Last-Event-ID') ?: ($request->query('lastEventId') ?: 0));
            $currentVersion = (int) ($state['version'] ?? 1);

            // Always send current state on connect.
            $send('commerce_state', $state, $currentVersion);
            $lastVersion = CommerceState::version();

            $start = time();
            $lastPing = 0;

            // Hold connection up to 1 hour (browser will reconnect automatically).
            while (!connection_aborted() && (time() - $start) < 3600) {
                $now = time();

                // Keep-alive ping every 15s
                if ($now - $lastPing >= 15) {
                    $send('ping', ['ts' => $now], null);
                    $lastPing = $now;
                }

                $version = CommerceState::version();
                if ($version !== $lastVersion) {
                    $state = CommerceState::current();
                    $currentVersion = (int) ($state['version'] ?? $version);
                    $send('commerce_state', $state, $currentVersion);
                    $lastVersion = $version;
                }

                usleep(2000000); // 2s
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Connection' => 'keep-alive',
            // Nginx: disable buffering if present
            'X-Accel-Buffering' => 'no',
        ]);

        return $response;
    }
}

