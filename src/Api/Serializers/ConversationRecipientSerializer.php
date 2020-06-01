<?php

namespace Kyrne\Shout\Api\Serializers;


use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Kyrne\Shout\ConversationUser;

class ConversationRecipientSerializer extends AbstractSerializer
{
    protected $type = 'conversation_users';

    protected function getDefaultAttributes($conversationUser)
    {
        if (!($conversationUser instanceof ConversationUser)) {
            throw new \InvalidArgumentException(
                get_class($this) . ' can only serialize instances of ' . ConversationUser::class
            );
        }

        return [
            'userId' => $conversationUser->user_id,
            'conversationId' => $conversationUser->conversation_id,
            'cipher' => $conversationUser->cipher
        ];
    }

    public function user($conversationUser)
    {
        return $this->hasOne($conversationUser, BasicUserSerializer::class);
    }

    public function conversation($conversationUser)
    {
        return $this->hasOne($conversationUser, ConversationSerializer::class);
    }
}