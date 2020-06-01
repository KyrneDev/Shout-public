<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\AssertPermissionTrait;
use Flarum\User\Exception\PermissionDeniedException;
use Kyrne\Shout\Message;

class HideMessageHandler
{
    use AssertPermissionTrait;

    public function handle(HideMessage $command)
    {
        $actor = $command->actor;
        $messageId = $command->messageId;

        $this->assertCan($actor, 'deleteMessage');

        $message = Message::find($messageId);

        if ($actor->id != $message->user_id) {
            throw new PermissionDeniedException;
        }

        $message->is_hidden = true;

        $message->save();
    }
}