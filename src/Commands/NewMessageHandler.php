<?php
/**
 *
 *  This file is part of kyrne/shout
 *
 *  Copyright (c) 2020 Kyrne.
 *
 *  For the full copyright and license information, please view the license.md
 *  file that was distributed with this source code.
 *
 */

namespace Kyrne\Shout\Commands;


use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Message;
use Pusher\Pusher;

class NewMessageHandler
{
    public function handle(NewMessage $command)
    {
        $actor = $command->actor;
        $data = $command->data;
        $conversationId = $command->conversationId;

        if ($conversationId) {
            $conversation = Conversation::find($conversationId);
        } else {
            $conversation = Conversation::find($data['attributes']['conversationId']);
        }

        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException;
        }

        $message = Message::newMessage(json_encode($data['attributes']['messageContents']), $actor->id,
            $conversation->id);

        $message->save();

        $conversation->increment('total_messages');

        ConversationUser::where([
            ['user_id', '=', $actor->id],
            ['conversation_id', '=', $conversation->id]
        ])->update([
            'cipher' => $data['attributes']['cipher']
        ]);

        foreach (ConversationUser::where('conversation_id', $conversation->id)->pluck('user_id')->all() as $userId) {
            if (intval($userId) !== intval($actor->id)) {
                User::find($userId)->increment('unread_messages');
                $this->pushNewMessage($userId, $message, $conversation->id);
            }
        }

        $id = $actor->id;

        $message->message = json_decode($message->message)->$id;

        return $message;
    }

    public function pushNewMessage($userId, $message, $conversationId)
    {
        if (app()->bound(Pusher::class)) {
            app(Pusher::class)->trigger('private-user' . $userId, 'newMessage', [
                'id' => $message->id,
                'message' => json_decode($message->message)->$userId,
                'createdAt' => (new \DateTime($message->created_at))->format(\DateTime::RFC3339),
                'conversationId' => $conversationId
            ]);
        }
    }
}