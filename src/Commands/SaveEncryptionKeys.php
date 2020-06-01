<?php

namespace Kyrne\Shout\Commands;

use Flarum\User\User;

class SaveEncryptionKeys
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
     * SaveEncryptionKeys constructor.
     * @param User $actor
     * @param array $data
     */
    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }
}