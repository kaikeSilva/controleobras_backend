# Laravel 12 Reverb WebSocket Test Implementation

## TASK OVERVIEW

Create a comprehensive test implementation for Laravel 12 Reverb WebSocket functionality. This will extend the existing TestController to include WebSocket testing capabilities alongside the current Redis/Queue/MailHog tests.

## SCOPE DEFINITION

**WHAT YOU WILL CREATE:**
- WebSocket test routes in existing TestController
- Real-time WebSocket test views
- WebSocket event classes for testing
- JavaScript WebSocket client functionality
- Integration with existing test interface

**WHAT YOU WILL NOT MODIFY:**
- Docker configuration files
- Database schemas or migrations
- Existing Redis/Queue/Mail functionality
- Reverb server configuration
- Broadcasting configuration (assume it's already set up)

## IMPLEMENTATION REQUIREMENTS

### Step 1: Create WebSocket Event Classes

**Create the following event classes for testing:**

1. **TestWebSocketEvent** - General WebSocket test event
2. **TestMessageEvent** - Message broadcasting test event
3. **TestNotificationEvent** - Notification broadcasting test event

**Laravel 12 Event Structure Requirements:**
- Use `ShouldBroadcast` interface
- Implement proper channel authorization
- Include test data payload
- Follow Laravel 12 broadcasting conventions

### Step 2: Extend TestController

**Add these methods to the existing TestController:**

1. **showWebSocketTest()** - Display WebSocket test interface
2. **sendWebSocketTest()** - Trigger WebSocket events
3. **getWebSocketStatus()** - Check WebSocket connection status
4. **broadcastTestMessage()** - Send test messages via WebSocket
5. **broadcastTestNotification()** - Send test notifications

**Controller Requirements:**
- Maintain existing Redis/Queue/Mail functionality
- Add WebSocket-specific error handling
- Include logging for WebSocket events
- Provide status monitoring for Reverb service

### Step 3: Create WebSocket Test Views

**Create these new Blade templates:**

1. **websocket-test.blade.php** - Main WebSocket testing interface
2. **websocket-status.blade.php** - WebSocket connection status component
3. **Update test-form.blade.php** - Add WebSocket testing section

**View Requirements:**
- Real-time connection status indicator
- WebSocket event testing forms
- Live message display area
- Connection diagnostics panel
- Integration with existing test interface

### Step 4: Implement JavaScript WebSocket Client

**JavaScript Requirements:**
- Laravel Echo integration with Reverb
- Real-time event listeners
- Connection status monitoring
- Automatic reconnection handling
- Event logging and display
- Integration with existing status refresh functionality

### Step 5: Add WebSocket Routes

**Add these routes to existing web.php:**
- GET /websocket-test - WebSocket test interface
- POST /send-websocket-test - Trigger WebSocket events
- GET /websocket-status - WebSocket connection status API
- POST /broadcast-test-message - Send test messages
- POST /broadcast-test-notification - Send test notifications

## TECHNICAL SPECIFICATIONS

### Laravel 12 Reverb Integration

**Use these Laravel 12 broadcasting features:**
- `ShouldBroadcast` interface for events
- Channel authorization in `channels.php`
- Laravel Echo for client-side connectivity
- Reverb-specific configuration assumptions

### WebSocket Event Structure

**Each event should include:**
- Unique event identifier
- Timestamp
- User/session information
- Test payload data
- Channel targeting

### Client-Side JavaScript

**Implement these features:**
- Laravel Echo initialization with Reverb
- Multiple event listeners for different test types
- Real-time UI updates
- Connection status monitoring
- Error handling and reconnection logic

### Integration with Existing Tests

**Ensure compatibility with:**
- Current Redis connection testing
- Queue status monitoring
- MailHog email testing
- Existing UI components and styling

## DOCKER CONTAINER CONSIDERATIONS

**Reverb Service:**
- Assume `websocket` container is running Reverb
- WebSocket server accessible on standard port
- Use container name `websocket` for internal communication

**Testing Commands:**
- Monitor WebSocket logs: `docker compose logs websocket`
- Check Reverb status via Laravel commands
- Verify broadcasting configuration

## EXPECTED DELIVERABLES

### Files to Create/Modify:

1. **app/Events/TestWebSocketEvent.php** - WebSocket test event
2. **app/Events/TestMessageEvent.php** - Message broadcasting event  
3. **app/Events/TestNotificationEvent.php** - Notification broadcasting event
4. **app/Http/Controllers/TestController.php** - Extend existing controller
5. **resources/views/websocket-test.blade.php** - WebSocket test interface
6. **resources/views/websocket-status.blade.php** - Status component
7. **resources/views/test-form.blade.php** - Update existing form
8. **routes/web.php** - Add WebSocket routes
9. **routes/channels.php** - Add channel authorization (if needed)

### Testing Verification:

1. **WebSocket Connection** - Verify Reverb connection works
2. **Event Broadcasting** - Test different event types
3. **Real-time Updates** - Confirm live UI updates
4. **Error Handling** - Test connection failures and recovery
5. **Integration** - Ensure existing tests still work

## SUCCESS CRITERIA

**The implementation is successful when:**
- WebSocket test interface loads and connects to Reverb
- Different event types broadcast successfully
- Real-time UI updates work correctly
- Connection status monitoring functions properly
- Integration with existing Redis/Queue/Mail tests remains intact
- All WebSocket events are logged and displayed properly
- Error handling provides meaningful feedback

## TESTING PROCESS

**Verification Steps:**
1. Access `/websocket-test` route
2. Verify WebSocket connection status shows "Connected"
3. Send test messages and verify real-time delivery
4. Test different event types (message, notification)
5. Monitor WebSocket logs for proper event handling
6. Verify existing `/test` functionality still works
7. Test error scenarios (connection loss, invalid events)

**Monitoring Commands:**
- `docker compose logs websocket` - Reverb server logs
- `docker compose logs php` - Laravel application logs
- Browser DevTools - WebSocket connection monitoring

## CONSTRAINTS REMINDER  

- Follow Laravel 12 conventions for all code
- Use existing Docker container architecture
- Maintain compatibility with current test functionality
- Implement proper error handling and logging
- Follow the agent-guidelines.md for execution approach
- Use Laravel artisan commands where appropriate
- Only request developer help when truly blocked by technical barriers 