<?php


namespace Kyrne\Shout;

use Flarum\Api\Controller\CreateUserController;
use Flarum\Api\Controller\ListUsersController;
use Flarum\Api\Controller\ShowUserController;
use Flarum\Api\Controller\UpdateUserController;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Api\Serializer\CurrentUserSerializer;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend;
use Flarum\User\User;
use Kyrne\Shout\Api\Controllers;
use Kyrne\Shout\Api\Serializers\ConversationRecipientSerializer;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/resources/js/admin.js'),
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/resources/js/forum.js')
        ->css(__DIR__.'/resources/less/extension.less')
        ->route('/shout/messages/{id}', 'shout.messages')
        ->route('/shout/conversations', 'shout.conversation'),
    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('canMessage', function (ForumSerializer $serializer) {
            return $serializer->getActor()->can('startConversation');
        }),
    (new Extend\ApiSerializer(BasicUserSerializer::class))
        ->attributes(function (BasicUserSerializer $serializer) {
            $newEncryption = Encryption::where('user_id', $serializer->getActor()->id)->first();
            $attributes['PMSetup'] = (bool)$newEncryption;
            $attributes['PrekeysExhausted'] = (bool)$newEncryption ? $newEncryption->prekeys_exhausted : false;
            return $attributes;
        }),
    (new Extend\ApiSerializer(CurrentUserSerializer::class))
        ->hasMany('conversations', ConversationRecipientSerializer::class)
        ->attributes(function (CurrentUserSerializer $serializer) {
            $newEncryption = Encryption::where('user_id', $serializer->getActor()->id)->first();
            $attributes['unreadMessages'] = $serializer->getActor()->unread_messages;
            $attributes['PrekeyIndex'] = $newEncryption ? $newEncryption->prekey_index : 0;
            return $attributes;
        }),


    (new Extend\ApiController(ListUsersController::class))
        ->addInclude('conversations'),
    (new Extend\ApiController(ShowUserController::class))
        ->addInclude('conversations'),
    (new Extend\ApiController(CreateUserController::class))
        ->addInclude('conversations'),
    (new Extend\ApiController(UpdateUserController::class))
        ->addInclude('conversations'),

    (new Extend\Settings())
        ->serializeToForum('kyrne-shout.shoutOwnPassword', 'kyrne-shout.set_own_password', 'boolVal', false)
        ->serializeToForum('kyrne-shout.shoutReturnKey', 'kyrne-shout.return_key', 'boolVal', false),

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
        ->patch('/shout/encryption', 'shout.keys.update', Controllers\UpdateKeysController::class)
        ->get('/shout/encryption/{id}', 'shout.keys.get', Controllers\GetIdentityController::class),
];
