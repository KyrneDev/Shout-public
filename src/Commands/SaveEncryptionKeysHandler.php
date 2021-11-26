<?php

namespace Kyrne\Shout\Commands;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;
use Kyrne\Shout\Encryption;

class SaveEncryptionKeysHandler
{
    public function handle(SaveEncryptionKeys $newMessage)
    {
        $actor = $newMessage->actor;
        $data = $newMessage->data;
        $key = resolve(SettingsRepositoryInterface::class)->get('kyrne-shout.set_own_password') ? resolve(Hasher::class)->make($data['password']) : '';
        $newEncryption = Encryption::populate($actor, $data['encryptedIdentity'], $data['preKeys'], $data['bundle'], $key);
        $newEncryption->save();
        return $newEncryption;
    }
}