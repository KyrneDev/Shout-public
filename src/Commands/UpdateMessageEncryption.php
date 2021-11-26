<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\User;

class UpdateMessageEncryption
{
    public $messageId;
    public $actor;
    public $data;

    public function __construct($messageId, User $actor, $data)
    {
        $this->messageId = $messageId;
        $this->actor = $actor;
        $this->data = $data;
    }
}