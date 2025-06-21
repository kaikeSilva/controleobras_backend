<?php

namespace App\Http\Controllers;

use App\Jobs\SendTestEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Events\TestWebSocketEvent;
use App\Events\TestMessageEvent;
use App\Events\TestNotificationEvent;

class TestController extends Controller
{
    public function showTestForm()
    {
        $redisTestStatus = $this->testRedisConnection();
        
        // Log Redis status for debugging
        Log::info('Redis Connection Status: ' . ($redisTestStatus ? 'Connected' : 'Failed'));

        return view('test-form', ['redisTestStatus' => $redisTestStatus]);
    }

    public function submitTestForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        $email = $request->input('email');
        $name = $request->input('name');
        $message = $request->input('message');

        try {
            // Dispatch job to the queue
            SendTestEmailJob::dispatch($email, $name, $message);
            Log::info('Job dispatched for email: ' . $email);

            // Set a success message in Redis (optional, for testing Redis write)
            Redis::set('last_test_email_sent_to', $email);
            Redis::expire('last_test_email_sent_to', 3600); // Expire in 1 hour

            return redirect()->route('test.form')
                             ->with('status', 'Test email queued successfully! Check MailHog and queue logs.');
        } catch (\Exception $e) {
            Log::error('Error dispatching job or setting Redis key: ' . $e->getMessage());
            return redirect()->route('test.form')
                             ->with('error', 'Failed to queue test email. Error: ' . $e->getMessage());
        }
    }

    private function testRedisConnection()
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            Log::error('Redis connection failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getStatus()
    {
        $redisTestStatus = $this->testRedisConnection();
        $lastEmailSent = Redis::get('last_test_email_sent_to');
        $queueLength = Redis::connection('default')->llen('queues:default'); // Assumes default queue name

        return response()->json([
            'redis_status' => $redisTestStatus ? 'Connected' : 'Disconnected',
            'last_email_sent_to' => $lastEmailSent ?? 'N/A',
            'queue_length' => $queueLength,
            'mailhog_url' => 'http://localhost:8025',
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    // WebSocket Test Methods Start

    public function showWebSocketTest()
    {
        Log::info('Accessing WebSocket test page.');
        return view('websocket-test'); // View to be created in Step 3
    }

    public function sendWebSocketTest(Request $request)
    {
        $request->validate([
            'event_data_key' => 'sometimes|string|max:255',
            'event_data_value' => 'sometimes|string|max:1000',
        ]);

        $payload = [
            'id' => uniqid(),
            'timestamp' => now()->toDateTimeString(),
            'user_info' => 'TestSessionID:' . session()->getId(), // Example user/session info
            'data' => $request->only(['event_data_key', 'event_data_value']),
            'event_type' => 'TestWebSocketEvent'
        ];

        try {
            TestWebSocketEvent::dispatch($payload);
            Log::info('TestWebSocketEvent dispatched.', ['payload' => $payload]);
            return response()->json(['status' => 'success', 'message' => 'TestWebSocketEvent dispatched successfully.']);
        } catch (\Exception $e) {
            Log::error('Error dispatching TestWebSocketEvent: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => 'Failed to dispatch TestWebSocketEvent: ' . $e->getMessage()], 500);
        }
    }

    public function broadcastTestMessage(Request $request)
    {
        $request->validate([
            'message_content' => 'required|string|max:1000',
        ]);

        $payload = [
            'id' => 'fsafsd123',
            'timestamp' => '2025-06-20 18:29:42',
            'user_info' => 'TestSessionID:123',
            'message' => 'message_content',
            'event_type' => 'TestMessageEvent'
        ];

        try {
            TestMessageEvent::dispatch($payload);
            Log::info('TestMessageEvent dispatched.', ['payload' => $payload]);
            return response()->json(['status' => 'success', 'message' => 'TestMessageEvent dispatched successfully.']);
        } catch (\Exception $e) {
            Log::error('Error dispatching TestMessageEvent: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => 'Failed to dispatch TestMessageEvent: ' . $e->getMessage()], 500);
        }
    }

    public function broadcastTestNotification(Request $request)
    {
        $request->validate([
            'notification_title' => 'required|string|max:255',
            'notification_body' => 'required|string|max:1000',
        ]);

        $payload = [
            'id' => uniqid(),
            'timestamp' => now()->toDateTimeString(),
            'user_info' => 'TestSessionID:' . session()->getId(),
            'notification' => $request->only(['notification_title', 'notification_body']),
            'event_type' => 'TestNotificationEvent'
        ];

        try {
            TestNotificationEvent::dispatch($payload);
            Log::info('TestNotificationEvent dispatched.', ['payload' => $payload]);
            return response()->json(['status' => 'success', 'message' => 'TestNotificationEvent dispatched successfully.']);
        } catch (\Exception $e) {
            Log::error('Error dispatching TestNotificationEvent: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['status' => 'error', 'message' => 'Failed to dispatch TestNotificationEvent: ' . $e->getMessage()], 500);
        }
    }

    public function getWebSocketStatus()
    {
        // This is a basic status check. A more robust check might involve an artisan command or direct Reverb health check.
        $reverbHost = config('broadcasting.connections.reverb.host', '0.0.0.0'); // Default from Reverb docs
        $reverbPort = config('broadcasting.connections.reverb.port', 8080);    // Default from Reverb docs
        $appId = config('broadcasting.connections.reverb.app_id');

        // Attempt to connect to Reverb's health check endpoint if available, or simply assume running.
        // For now, we'll provide configured details and an assumed status.
        $status = 'assumed_operational';
        $message = 'Reverb status is based on configuration. Verify Reverb service and logs for actual status.';
        
        // Placeholder for a more advanced check, e.g., using Guzzle to ping Reverb's /apps/{APP_ID}/status endpoint
        // if it's exposed and accessible from the PHP container.
        // For this iteration, we rely on the user to check `docker compose logs websocket` or `php artisan reverb:status`.

        Log::info('Reporting WebSocket status.', ['reverb_host' => $reverbHost, 'reverb_port' => $reverbPort, 'app_id' => $appId]);
        return response()->json([
            'reverb_status' => $status,
            'reverb_app_id' => $appId,
            'reverb_configured_host' => $reverbHost,
            'reverb_configured_port' => $reverbPort,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
    // WebSocket Test Methods End
}
