<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Canal privado para notificações de PDF do usuário
Broadcast::channel('pdf.{userId}', function ($user, $userId) {
    // Usuário só pode acessar seu próprio canal
    return (int) $user->id === (int) $userId;
});

// Canal padrão do Laravel (manter o existente)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal de teste (manter o existente se houver)
Broadcast::channel('message-channel', function ($user) {
    return true; // ou sua lógica específica
});
