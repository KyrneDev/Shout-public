<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\User;

class HideMessage
{
    public $messageId;
    public $actor;

    public function __construct($messageId, User $actor)
    {
        $this->messageId = $messageId;
        $this->actor = $actor;
    }
}