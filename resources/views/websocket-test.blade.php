<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token for AJAX -->
    <title>Laravel Reverb WebSocket Test</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    {{-- Remove CDN Echo and Pusher, Vite will provide them --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script> --}}
    {{-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Add Vite directive --}}
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f6f8; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; text-align: center; }
        .status-indicator { padding: 10px; margin-bottom: 15px; border-radius: 5px; text-align: center; font-weight: bold; }
        .status-connected { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-disconnected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-connecting { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .form-section, .log-section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type='text'], input[type='email'], textarea, select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button { background-color: #3498db; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        button:hover { background-color: #2980b9; }
        #messagesArea, #eventLogArea { height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px; background: #f9f9f9; margin-top: 10px; }
        .log-entry { padding: 5px; border-bottom: 1px dashed #eee; font-size: 0.9em; }
        .log-entry:last-child { border-bottom: none; }
        .nav-links { text-align: center; margin-bottom: 20px; }
        .nav-links a { margin: 0 10px; color: #3498db; text-decoration: none; font-weight: bold; }
        .nav-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="nav-links">
            <a href="{{ route('test.form') }}">Redis/Queue/Mail Test</a>
            <a href="{{ route('websocket.test.show') }}">WebSocket Test</a>
        </div>

        <h1 class="text-3xl font-bold text-center mb-6">Laravel Reverb WebSocket Test</h1>

        <!-- WebSocket Status Component Placeholder -->
        <div id="websocketStatusComponent">
            @include('websocket-status', ['status' => 'connecting', 'app_id' => 'N/A', 'host' => 'N/A', 'port' => 'N/A', 'message' => 'Attempting to connect...'])
        </div>

        <!-- Test Event Forms -->
        <div class="form-section">
            <h2 class="text-2xl font-semibold mb-4">Send Test Events</h2>
            
            <!-- Generic WebSocket Event -->
            <form id="sendTestEventForm" class="mb-4">
                <h3 class="text-xl mb-2">Generic Test Event</h3>
                <div>
                    <label for="event_data_key">Event Data Key (Optional):</label>
                    <input type="text" id="event_data_key" name="event_data_key" class="mt-1 block w-full" placeholder="e.g., user_id">
                </div>
                <div>
                    <label for="event_data_value">Event Data Value (Optional):</label>
                    <input type="text" id="event_data_value" name="event_data_value" class="mt-1 block w-full" placeholder="e.g., 123">
                </div>
                <button type="submit" class="mt-2">Send Generic Event</button>
            </form>

            <!-- Test Message Event -->
            <form id="broadcastMessageForm" class="mb-4">
                <h3 class="text-xl mb-2">Broadcast Test Message</h3>
                <div>
                    <label for="message_content">Message Content:</label>
                    <textarea id="message_content" name="message_content" rows="3" class="mt-1 block w-full" required placeholder="Enter your message here..."></textarea>
                </div>
                <button type="submit" class="mt-2">Broadcast Message</button>
            </form>

            <!-- Test Notification Event -->
            <form id="broadcastNotificationForm">
                <h3 class="text-xl mb-2">Broadcast Test Notification</h3>
                <div>
                    <label for="notification_title">Notification Title:</label>
                    <input type="text" id="notification_title" name="notification_title" class="mt-1 block w-full" required placeholder="e.g., New Update">
                </div>
                <div>
                    <label for="notification_body">Notification Body:</label>
                    <textarea id="notification_body" name="notification_body" rows="2" class="mt-1 block w-full" required placeholder="Details of the notification..."></textarea>
                </div>
                <button type="submit" class="mt-2">Broadcast Notification</button>
            </form>
        </div>

        <!-- Live Message Display Area -->
        <div class="log-section">
            <h2 class="text-2xl font-semibold mb-2">Live Messages / Events</h2>
            <div id="messagesArea">Waiting for messages...</div>
        </div>

        <!-- Connection Diagnostics / Event Log -->
        <div class="log-section">
            <h2 class="text-2xl font-semibold mb-2">Event Log & Diagnostics</h2>
            <div id="eventLogArea">WebSocket client logs will appear here...</div>
        </div>
    </div>

    <script type="module"> 
        // Laravel Echo and WebSocket client implementation
        const csrfToken = document.querySelector('meta[name="csrf-token"').getAttribute('content');

        // Echo is initialized globally by resources/js/echo.js via Vite's app.js
        // We will use window.Echo directly. Ensure app.js is loaded before this script runs.
        // Remove the manual Echo instantiation that was here.

        // Function to log events to the page
        const eventLogArea = document.getElementById('eventLogArea');
        function logEvent(message) {
            console.log(message); // Also log to console for easier debugging
            const entry = document.createElement('div');
            entry.classList.add('log-entry');
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            eventLogArea.appendChild(entry);
            eventLogArea.scrollTop = eventLogArea.scrollHeight; // Scroll to bottom
        }

        // Function to display received messages
        const messagesArea = document.getElementById('messagesArea');
        function displayMessage(data) {
            const entry = document.createElement('div');
            entry.classList.add('log-entry');
            entry.textContent = JSON.stringify(data);
            messagesArea.appendChild(entry);
            messagesArea.scrollTop = messagesArea.scrollHeight; // Scroll to bottom
        }

        // Wait for Echo to be available if app.js loads asynchronously
        // A more robust way is to ensure app.js is loaded and executed first, 
        // or to use a custom event dispatched by app.js when Echo is ready.
        // For simplicity here, we'll assume window.Echo is available shortly.
        
        function initializeWebSocketClient() {
            if (typeof window.Echo === 'undefined') {
                logEvent('Echo instance not found. Waiting...');
                setTimeout(initializeWebSocketClient, 100); // Retry after 100ms
                return;
            }

            logEvent('Echo client found. Attempting to connect...');

            // Connection status listeners using the global window.Echo instance
            window.Echo.connector.pusher.connection.bind('connecting', () => {
                logEvent('Echo: Connecting to Reverb...');
                updateWebSocketStatusDisplay('connecting', 'Connecting...');
            });

            window.Echo.connector.pusher.connection.bind('connected', () => {
                logEvent('Echo: Connected to Reverb successfully!');
                updateWebSocketStatusDisplay('connected', 'Connection established.');
                subscribeToChannels();
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                logEvent('Echo: Disconnected from Reverb.');
                updateWebSocketStatusDisplay('disconnected', 'Connection lost. Will attempt to reconnect.');
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                logEvent('Echo: Connection Error - ' + JSON.stringify(err));
                let message = 'Connection error.';
                if (err.error && err.error.data && err.error.data.message) {
                    message = err.error.data.message;
                }
                updateWebSocketStatusDisplay('error', message);
            });
        }
        
        function subscribeToChannels() {
            window.Echo.channel('test-channel')
                .listen('.test.websocket.event', (e) => {
                    logEvent('Received TestWebSocketEvent: ' + JSON.stringify(e));
                    displayMessage(e);
                });
            logEvent('Subscribed to test-channel for .test.websocket.event');

            window.Echo.channel('message-channel')
                .listen('.test.message.event', (e) => {
                    logEvent('Received TestMessageEvent: ' + JSON.stringify(e));
                    displayMessage(e);
                });
            logEvent('Subscribed to message-channel for .test.message.event');

            window.Echo.channel('notification-channel')
                .listen('.test.notification.event', (e) => {
                    logEvent('Received TestNotificationEvent: ' + JSON.stringify(e));
                    displayMessage(e);
                });
            logEvent('Subscribed to notification-channel for .test.notification.event');
        }

        // Form submission handlers
        document.getElementById('sendTestEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            logEvent('Attempting to send Generic Test Event: ' + JSON.stringify(data));
            fetch('{{ route("websocket.test.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                logEvent('Generic Event Sent: ' + JSON.stringify(result));
                if(result.status === 'error') alert('Error: ' + result.message);
            })
            .catch(error => {
                logEvent('Error sending generic event: ' + error);
                alert('Network or server error sending generic event.');
            });
        });

        document.getElementById('broadcastMessageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            logEvent('Attempting to broadcast Test Message: ' + JSON.stringify(data));
            fetch('{{ route("websocket.test.message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                logEvent('Test Message Broadcast: ' + JSON.stringify(result));
                if(result.status === 'error') alert('Error: ' + result.message);
            })
            .catch(error => {
                logEvent('Error broadcasting message: ' + error);
                alert('Network or server error broadcasting message.');
            });
        });

        document.getElementById('broadcastNotificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            logEvent('Attempting to broadcast Test Notification: ' + JSON.stringify(data));
            fetch('{{ route("websocket.test.notification") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                logEvent('Test Notification Broadcast: ' + JSON.stringify(result));
                if(result.status === 'error') alert('Error: ' + result.message);
            })
            .catch(error => {
                logEvent('Error broadcasting notification: ' + error);
                alert('Network or server error broadcasting notification.');
            });
        });

        // Function to update WebSocket status display
        function updateWebSocketStatusDisplay(status, message) {
            const statusComponent = document.getElementById('websocketStatusComponent');
            let statusClass = 'status-disconnected';
            let appId = 'N/A';
            let host = 'N/A';
            let port = 'N/A';

            // Get connection details from the Echo instance if available
            if (window.Echo && window.Echo.options) {
                appId = window.Echo.options.key; // VITE_REVERB_APP_KEY is used as 'key'
                host = window.Echo.options.wsHost;
                port = window.Echo.options.wsPort;
            }

            if (status === 'connected') statusClass = 'status-connected';
            if (status === 'connecting') statusClass = 'status-connecting';

            statusComponent.innerHTML = `
                <div class="status-indicator ${statusClass}">
                    <strong>Status:</strong> ${status.charAt(0).toUpperCase() + status.slice(1)}<br>
                    <strong>App ID:</strong> ${appId}<br>
                    <strong>Host:</strong> ${host}<br>
                    <strong>Port:</strong> ${port}<br>
                    <strong>Message:</strong> ${message}
                </div>
            `;
        }

        // Initial call to set up status and start connection attempts
        document.addEventListener('DOMContentLoaded', () => {
            updateWebSocketStatusDisplay('connecting', 'Initializing and attempting to connect...');
            initializeWebSocketClient();
        });

    </script>
</body>
</html>
