<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Message;
use Pusher\Pusher;

class NewMessageHandler
{
    public function handle(NewMessage $newMessage)
    {
        $actor = $newMessage->actor;
        $data = $newMessage->data;
        $conversationId = $newMessage->conversationId;
        if ($conversationId) {
            $conversation = Conversation::find($conversationId);
        } else {
            $conversation = Conversation::find($data['attributes']['conversationId']);
        }
        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException();
        }
        $response = Message::newMessage(json_encode($data['attributes']['messageContents']), $actor->id, $conversation->id);
        $response->save();
        $conversation->increment('total_messages');
        ConversationUser::where(array(array('user_id', '=', $actor->id), array('conversation_id', '=', $conversation->id)))->update(array('cipher' => $data['attributes']['cipher']));
        foreach (ConversationUser::where('conversation_id', $conversation->id)->pluck('user_id')->all() as $userId) {
            if (intval($userId) !== intval($actor->id)) {
                User::find($userId)->increment('unread_messages');
                $this->pushNewMessage($userId, $response, $conversation->id);
            }
        }
        $encryptionKeyId = $actor->id;
        $response->message = json_decode($response->message)->{$encryptionKeyId};
        return $response;
    }

    public function pushNewMessage($userId, $response, $conversationId)
    {
        if (app()->bound(Pusher::class)) {
            app(Pusher::class)->trigger('private-user' . $userId, 'newMessage', array('id' => $response->id, 'message' => json_decode($response->message)->{$userId}, 'createdAt' => (new \DateTime($response->created_at))->format(\DateTime::RFC3339), 'conversationId' => $conversationId));
        }
    }
}