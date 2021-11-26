<?php

namespace Kyrne\Shout;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;
use Ramsey\Uuid\Uuid;

class Conversation extends AbstractModel
{
    protected $table = 'conversations';
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'string';
    public $fillable = array('user_one_id', 'user_two_id', 'status');
    protected $dates = array('created_at', 'updated_at');

    public static function start()
    {
        $conversation = new static();
        $conversation->id = Uuid::uuid4()->toString();
        $conversation->created_at = Carbon::now();
        return $conversation;
    }

    public static function findOrFail($encryptionKeyId)
    {
        $spf7b294 = static::where('id', $encryptionKeyId);
        return $spf7b294->firstOrFail();
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id')->with('user');
    }

    public function recipients()
    {
        return $this->hasMany(ConversationUser::class, 'conversation_id')->with('user');
    }
}