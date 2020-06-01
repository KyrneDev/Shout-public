<?php

namespace Kyrne\Shout\Commands;


use Flarum\User\User;

class HideMessage
{
    /**
     * @var string
     */
    public $messageId;

    /**
     * @var User
     */
    public $actor;

    /**
     * HideMessage constructor.
     * @param $messageId
     * @param User $actor
     */
    public function __construct($messageId, User $actor)
    {
        $this->messageId = $messageId;
        $this->actor = $actor;
    }
}