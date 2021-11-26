<?php

namespace Kyrne\Shout;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;

class Encryption extends AbstractModel
{
    protected $table = 'user_encryption_keys';
    public $timestamps = true;
    protected $dates = array('created_at', 'updated_at');
    protected $hidden = array('private_key');

    public static function populate($actor, $identityKey, $sp75722f, $sp2c476a, $key)
    {
        $newEncryption = new static();
        $newEncryption->user_id = $actor->id;
        $newEncryption->identity_key = $identityKey;
        $newEncryption->prekeys = json_encode($sp75722f);
        $newEncryption->bundleProto = $sp2c476a;
        $newEncryption->key = $key;
        $newEncryption->created_at = Carbon::now();
        return $newEncryption;
    }

    public static function findOrFail($encryptionKeyId)
    {
        $spf7b294 = static::where('id', $encryptionKeyId);
        return $spf7b294->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}