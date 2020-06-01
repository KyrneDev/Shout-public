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

namespace Kyrne\Shout\Api\Serializers;


use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Kyrne\Shout\Message;

class MessageSerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'messages';

    /**
     * @param $group
     *
     * @return array
     */
    protected function getDefaultAttributes($message)
    {
        if (!($message instanceof Message)) {
            throw new \InvalidArgumentException(
                get_class($this).' can only serialize instances of '.Message::class
            );
        }

        return [
            'message' => (bool) !$message->is_hidden ? $message->message : '', //Todo Add translator,
            'userId' => $message->user_id,
            'isHidden' => $message->is_hidden,
            'createdAt' => $this->formatDate($message->created_at),
            'conversationId' => $message->conversation_id
        ];
    }

    protected function user($message)
    {
        return $this->hasOne($message, BasicUserSerializer::class);
    }
}