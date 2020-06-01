<?php

namespace Kyrne\Shout\Commands;


use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Message;

class UpdateMessageEncryptionHandler
{
    public function handle(UpdateMessageEncryption $command)
    {
        $actor = $command->actor;
        $data = $command->data;
        $messageId = $command->messageId;

        $message = Message::findOrFail($messageId);
        $conversation = Conversation::findOrFail($message->conversation_id);

        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException;
        }

        $actorId = $actor->id;

        $messageContent = json_decode($message->message);

        $messageContent->$actorId = $data['message'];
        $message->message = json_encode($messageContent);

        ConversationUser::where([['user_id', $actor->id], ['conversation_id', $conversation->id]])->update([
            'cipher' => $data['encryptedCipher']
        ]);

        User::find($actor->id)->decrement('unread_messages');

        $message->save();

        $id = $actor->id;

        $message->message = json_decode($message->message)->$id;

        return $message;
    }
}