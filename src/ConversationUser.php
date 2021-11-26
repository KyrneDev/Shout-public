<?php

namespace Kyrne\Shout;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class ConversationUser extends AbstractModel
{
    protected $table = 'conversation_user';

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}