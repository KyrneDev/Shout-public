<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\User;

class NewMessage
{
    public $actor;
    public $data;
    public $conversationId;

    public function __construct(User $actor, array $data, $conversationId = null)
    {
        $this->actor = $actor;
        $this->data = $data;
        $this->conversationId = $conversationId;
    }
}