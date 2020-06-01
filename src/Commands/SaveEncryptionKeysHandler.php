<?php

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