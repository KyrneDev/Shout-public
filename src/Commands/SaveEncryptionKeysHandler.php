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


use Kyrne\Shout\Encryption;

class SaveEncryptionKeysHandler
{
    public function handle(SaveEncryptionKeys $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $keys = Encryption::populate($actor, $data['encryptedIdentity'], $data['preKeys'], $data['bundle']);

        $keys->save();


        $actor->PMSetup = 1;
        $actor->save();

        return $keys;
    }
}