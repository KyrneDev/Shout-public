<?php

namespace Kyrne\Shout\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Kyrne\Shout\Message;

class MessageSerializer extends AbstractSerializer
{
    protected $type = 'messages';

    protected function getDefaultAttributes($response)
    {
        if (!$response instanceof Message) {
            throw new \InvalidArgumentException(get_class($this) . ' can only serialize instances of ' . Message::class);
        }
        return array('message' => (bool)(!$response->is_hidden) ? $response->message : '', 'userId' => $response->user_id, 'isHidden' => $response->is_hidden, 'createdAt' => $this->formatDate($response->created_at), 'conversationId' => $response->conversation_id);
    }

    protected function user($response)
    {
        return $this->hasOne($response, BasicUserSerializer::class);
    }
}