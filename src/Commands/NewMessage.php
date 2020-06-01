<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\User;

class NewMessage
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var array
     */
    public $data;

    /**
     * @var null
     */
    public $conversationId;

    /**
     * NewMessage constructor.
     * @param User $actor
     * @param array $data
     * @param null $conversationId
     */
    public function __construct(User $actor, array $data, $conversationId = null)
    {
        $this->actor = $actor;
        $this->data = $data;
        $this->conversationId = $conversationId;
    }
}