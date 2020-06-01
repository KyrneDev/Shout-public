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


use Flarum\User\User;

class UpdateMessageEncryption
{
    /**
     * @var integer
     */
    public $messageId;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $data;

    /**
     * UpdateMessageEncryption constructor.
     * @param $messageId
     * @param User $actor
     * @param $data
     */
    public function __construct($messageId, User $actor, $data)
    {
        $this->messageId = $messageId;
        $this->actor = $actor;
        $this->data = $data;
    }
}