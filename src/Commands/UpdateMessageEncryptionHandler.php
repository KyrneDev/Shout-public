<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Message;

class UpdateMessageEncryptionHandler
{
    public function handle(UpdateMessageEncryption $newMessage)
    {
        $actor = $newMessage->actor;
        $data = $newMessage->data;
        $messageId = $newMessage->messageId;
        $response = Message::findOrFail($messageId);
        $conversation = Conversation::findOrFail($response->conversation_id);
        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException();
        }
        $sp2d38d2 = $actor->id;
        $spd59ffe = json_decode($response->message);
        $spd59ffe->{$sp2d38d2} = $data['message'];
        $response->message = json_encode($spd59ffe);
        ConversationUser::where(array(array('user_id', $actor->id), array('conversation_id', $conversation->id)))->update(array('cipher' => $data['encryptedCipher']));
        $sp0faaa1 = User::find($actor->id);
        if ($sp0faaa1->unread_messages > 0) {
            $sp0faaa1->decrement('unread_messages');
        }
        $response->save();
        $encryptionKeyId = $actor->id;
        $response->message = json_decode($response->message)->{$encryptionKeyId};
        return $response;
    }
}