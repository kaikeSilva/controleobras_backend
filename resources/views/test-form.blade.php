<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Redis Queue MailHog Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-dot.connected {
            background-color: #34D399; /* Tailwind green-400 */
        }
        .status-dot.disconnected {
            background-color: #F87171; /* Tailwind red-400 */
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem;
            border-radius: 0.5rem;
            color: white;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .toast.success {
            background-color: #10B981; /* Tailwind green-600 */
        }
        .toast.error {
            background-color: #EF4444; /* Tailwind red-500 */
        }
        .toast.show {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="nav-links text-center mb-6">
            <a href="{{ route('test.form') }}" class="mx-2 text-blue-600 hover:underline font-semibold">Redis/Queue/Mail Test</a>
            <a href="{{ route('websocket.test.show') }}" class="mx-2 text-blue-600 hover:underline font-semibold">WebSocket Test</a>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">
            Laravel Redis Queue MailHog Test
        </h1>

        <!-- Status Indicators -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-3">System Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center">
                    <span id="redis-status-dot" class="status-dot"></span>
                    <span class="font-medium">Redis:</span>
                    <span id="redis-status-text" class="ml-2">Checking...</span>
                </div>
                <div class="flex items-center">
                    <span class="font-medium">MailHog:</span>
                    <a href="http://localhost:8025" target="_blank" id="mailhog-link" class="ml-2 text-blue-600 hover:underline">Open MailHog (localhost:8025)</a>
                </div>
                <div class="flex items-center">
                    <span class="font-medium">Queue Length:</span>
                    <span id="queue-length" class="ml-2">Checking...</span>
                </div>
                <div class="flex items-center">
                    <span class="font-medium">Last Email Dispatched To:</span>
                    <span id="last-email" class="ml-2">Checking...</span>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                Last updated: <span id="last-updated">Never</span>
                <button onclick="refreshStatus()" class="ml-2 px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Refresh</button>
            </div>
        </div>

        @if(session('status'))
            <div id="toast-success" class="toast success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @if(session('error'))
            <div id="toast-error" class="toast error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('test.submit') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       value="{{ old('name', 'Test User') }}">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
                <input type="email" name="email" id="email" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       value="{{ old('email', 'test@example.com') }}">
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Test Message</label>
                <textarea name="message" id="message" rows="4" required
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('message', 'This is a test message for Laravel Queue with Redis and MailHog.') }}</textarea>
                @error('message')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Send Test Email via Queue
                </button>
            </div>
        </form>

        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-2 text-blue-800">Docker Testing Instructions:</h3>
            <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                <li>Queue worker is running in <code class="bg-white px-1 rounded">queue</code> container</li>
                <li>Redis is running in <code class="bg-white px-1 rounded">redis</code> container</li>
                <li>MailHog is accessible at <a href="http://localhost:8025" target="_blank" class="text-blue-600 hover:underline">localhost:8025</a></li>
                <li>Check queue logs: <code class="bg-white px-1 rounded">docker compose logs -f queue</code></li>
                <li>Check application logs: <code class="bg-white px-1 rounded">docker compose logs -f php</code> (for errors or info)</li>
            </ol>
        </div>
    </div>

    <script>
        async function refreshStatus() {
            try {
                const response = await fetch('{{ route('test.status') }}');
                const data = await response.json();

                const redisDot = document.getElementById('redis-status-dot');
                const redisText = document.getElementById('redis-status-text');
                if (data.redis_status === 'Connected') {
                    redisDot.className = 'status-dot connected';
                    redisText.textContent = 'Connected';
                } else {
                    redisDot.className = 'status-dot disconnected';
                    redisText.textContent = 'Disconnected';
                }

                document.getElementById('queue-length').textContent = data.queue_length;
                document.getElementById('last-email').textContent = data.last_email_sent_to;
                document.getElementById('mailhog-link').href = data.mailhog_url;
                document.getElementById('last-updated').textContent = data.timestamp;

            } catch (error) {
                console.error('Error refreshing status:', error);
                document.getElementById('redis-status-text').textContent = 'Error';
                document.getElementById('queue-length').textContent = 'Error';
                document.getElementById('last-email').textContent = 'Error';
            }
        }

        function showToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 5000); // Hide after 5 seconds
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            refreshStatus(); // Initial status check
            setInterval(refreshStatus, 15000); // Refresh status every 15 seconds

            // Show toasts if they exist
            if (document.getElementById('toast-success')) {
                showToast('toast-success');
            }
            if (document.getElementById('toast-error')) {
                showToast('toast-error');
            }
        });
    </script>
</body>
</html>
