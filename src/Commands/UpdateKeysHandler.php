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


use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;
use Kyrne\Shout\Encryption;

class UpdateKeysHandler
{
    public function handle(UpdateKeys $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $keys = Encryption::where('user_id', $actor->id)->firstOrFail();

        $keys->prekeys = json_encode($data['preKeys']);
        $keys->prekey_index = 0;
        $keys->bundle_proto = $data['bundle'];
        $keys->identity_key = $data['encryptedIdentity'];

        $keys->save();

        return $keys;
    }
}