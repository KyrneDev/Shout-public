<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\Exception\PermissionDeniedException;
use Kyrne\Shout\Message;

class HideMessageHandler
{
    public function handle(HideMessage $newMessage)
    {
        $actor = $newMessage->actor;
        $messageId = $newMessage->messageId;
        $actor->assertCan('deleteMessage');
        $response = Message::find($messageId);
        if ($actor->id != $response->user_id) {
            throw new PermissionDeniedException();
        }
        $response->is_hidden = true;
        $response->save();
    }
}