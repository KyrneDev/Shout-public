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

class SaveEncryptionKeysHandler
{
    public function handle(SaveEncryptionKeys $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $password = app(SettingsRepositoryInterface::class)->get('kyrne-shout.set_own_password') ?  app(Hasher::class)->make($data['password']) : '';

        $keys = Encryption::populate($actor, $data['encryptedIdentity'], $data['preKeys'], $data['bundle'], $password);

        $keys->save();


        $actor->PMSetup = 1;
        $actor->save();

        return $keys;
    }
}