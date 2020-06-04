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

namespace Kyrne\Shout;

use Carbon\Carbon;
use Flarum\Database\AbstractModel;
use Flarum\User\User;

class Encryption extends AbstractModel
{
    protected $table = 'user_encryption_keys';

    public $timestamps = true;

    protected $dates = ['created_at', 'updated_at'];

    protected $hidden = ['private_key'];

    public static function populate($actor, $encryptedIdentity, $prekeys, $bundle, $password)
    {
        $keys = new static;

        $keys->user_id = $actor->id;
        $keys->identity_key = $encryptedIdentity;
        $keys->prekeys = json_encode($prekeys);
        $keys->bundle_proto = $bundle;
        $keys->key = $password;
        $keys->created_at = Carbon::now();

        return $keys;
    }

    public static function findOrFail($id)
    {
        $query = static::where('id', $id);

        return $query->firstOrFail();
    }

    public function user()
    {
        return $this->belongsTo(User::class,  'user_id');
    }
}
