<?php

namespace Kyrne\Shout;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;

class Conversation extends AbstractModel
{
    protected $table = 'conversations';

    public $timestamps = true;

    public $fillable = [
        'user_one_id',
        'user_two_id',
        'status',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public static function start()
    {
        $conversation = new static;

        $conversation->created_at = Carbon::now();

        return $conversation;
    }

    public static function findOrFail($id)
    {
        $query = static::where('id', $id);

        return $query->firstOrFail();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id')
            ->with('user');
    }

    public function recipients() {
        return $this->hasMany(ConversationUser::class, 'conversation_id')
            ->with('user');
    }
}
