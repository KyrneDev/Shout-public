<?php


namespace Kyrne\Shout;

use Flarum\Extend;
use Flarum\Formatter\Event\Configuring;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Kyrne\Shout\Api\Controllers;
use Kyrne\ExtCore\Extend\AddKyrneCore;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/resources/js/admin.js'),
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/resources/js/forum.js')
        ->css(__DIR__.'/resources/less/extension.less')
        ->route('/shout/messages/{id}', 'shout.messages')
        ->route('/shout/conversations', 'shout.conversation'),
    new Extend\Locales(__DIR__.'/resources/locale'),
    new AddKyrneCore(),
    (new Extend\Model(User::class))
        ->hasMany('conversations' ,ConversationUser::class, 'user_id'),
    (new Extend\Routes('api'))
        ->get('/shout/conversations', 'shout.conversations.index', Controllers\ListConversationsController::class)
        ->get('/shout/messages/{id}', 'shout.messages.list', Controllers\ListMessagesController::class)
        ->post('/shout/conversations', 'shout.conversations.create', Controllers\CreateConversationController::class)
        ->post('/shout/messages', 'shout.messages.create', Controllers\CreateMessageController::class)
        ->post('/shout/messages/typing', 'shout.message.typing', Controllers\TypingPusherController::class)
        ->patch('/shout/messages/{id}', 'shout.messages.ecnryption.update', Controllers\UpdateMessageEncryptionController::class)
        ->delete('/shout/messages{id}', 'shout.messages.delete', Controllers\DeleteMessageController::class)
        //->patch('/messages/{id}', 'messages.update', Controllers\UpdateMessageController::class)
        //->delete('/messages/{id}', 'messages.delete', Controllers\DeleteMessageController::class)
        ->get('/shout/conversations/{id}', 'shout.conversations.show', Controllers\ShowConversationController::class)
        ->post('/shout/verifyPassword', 'shout.password.verify', Controllers\VerifyPasswordController::class)
        ->post('/shout/encryption', 'shout.keys.populate', Controllers\SaveEncryptionKeysController::class)
        ->patch('/shout/encryption', 'shout.keys.populate', Controllers\UpdateKeysController::class)
        ->get('/shout/encryption/{id}', 'shout.keys.get', Controllers\GetIdentityController::class),
    function (Dispatcher $events) {
        $events->subscribe(Listeners\AddRelationships::class);
    },
];
