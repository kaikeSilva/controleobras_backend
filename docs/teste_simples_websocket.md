conectar no canal de teste com o wscat:
wscat -c "ws://localhost:8087/app/demo_key?protocol=7&client=js&version=5.0.0"
{"event":"pusher:subscribe","data":{"channel":"message-channel"}}

acessar o container da aplicacao e enviar um evento de teste:
docker exec -it 30bc764bab9d4f1c7207d097d3d572b4286de8e47dbd30f2bd8cb5fc9e7c7b6d bash
php artisan tinker

enviar um evento de teste:
event(new \App\Events\TestMessageEvent([
    'id' => 'abc123',
    'timestamp' => now()->toDateTimeString(),
    'user_info' => 'TestSessionID:123',
    'message' => 'Mensagenzinha 🚀',
    'event_type' => 'TestMessageEvent',
]));

verificar se o evento cchega no wscat:
{"event":"test.message.event","data":"{\"id\":\"abc123\",\"timestamp\":\"2025-06-22 07:42:02\",\"user_info\":\"TestSessionID:123\",\"message\":\"Mensagenzinha \\ud83d\\ude80\",\"event_type\":\"TestMessageEvent\"}","channel":"message-channel"}