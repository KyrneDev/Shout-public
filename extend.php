<?php


namespace Kyrne\Shout;

use Flarum\Extend;
use Illuminate\Contracts\Events\Dispatcher;
use Kyrne\Shout\Api\Controllers;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/resources/js/admin.js'),
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/resources/js/forum.js')
        ->css(__DIR__.'/resources/less/extension.less')
        ->route('/messages/{id}', 'messages')
        ->route('/conversations', 'conversation'),
    new Extend\Locales(__DIR__.'/resources/locale'),
    (new Extend\Routes('api'))
        ->get('/conversations', 'conversations.index', Controllers\ListConversationsController::class)
        ->get('/messages/{id}', 'messages.list', Controllers\ListMessagesController::class)
        ->post('/conversations', 'conversations.create', Controllers\CreateConversationController::class)
        ->post('/messages', 'messages.create', Controllers\CreateMessageController::class)
        ->post('/messages/typing', 'message.typing', Controllers\TypingPusherController::class)
        ->patch('/messages/{id}', 'messages.ecnryption.update', Controllers\UpdateMessageEncryptionController::class)
        ->delete('/messages{id}', 'messages.delete', Controllers\DeleteMessageController::class)
        //->patch('/messages/{id}', 'messages.update', Controllers\UpdateMessageController::class)
        ->delete('/messages/{id}', 'messages.delete', Controllers\DeleteMessageController::class)
        ->get('/conversations/{id}', 'conversations.show', Controllers\ShowConversationController::class)
        ->post('/verifyPassword', 'password.verify', Controllers\VerifyPasswordController::class)
        ->post('/encryption', 'keys.populate', Controllers\SaveEncryptionKeysController::class)
        ->get('/encryption/{id}', 'keys.get', Controllers\GetIdentityController::class),
    function (Dispatcher $events) {
        $events->subscribe(Listeners\AddRelationships::class);

        //$events->subscribe(Access\MessagePolicy::class);
    },
];
