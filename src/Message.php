<?php

namespace Kyrne\Shout;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;
use Kyrne\Shout\Conversation;
use Ramsey\Uuid\Uuid;

class Message extends AbstractModel
{
    protected $table = 'messages';
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = array('human_time');
    protected $dates = array('created_at');
    public $fillable = array('message', 'is_seen', 'is_hidden', 'user_id', 'conversation_id');

    public static function newMessage($spd59ffe, $sp8e43de, $conversationId)
    {
        $response = new static();
        $response->id = Uuid::uuid4()->toString();
        $response->message = $spd59ffe;
        $response->user_id = $sp8e43de;
        $response->created_at = Carbon::now();
        $response->conversation_id = $conversationId;
        return $response;
    }

    public static function findOrFail($encryptionKeyId)
    {
        $spf7b294 = static::where('id', $encryptionKeyId);
        return $spf7b294->firstOrFail();
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}