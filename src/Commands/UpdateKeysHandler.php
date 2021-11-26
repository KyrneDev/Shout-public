<?php

namespace Kyrne\Shout\Commands;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Hashing\Hasher;
use Kyrne\Shout\Encryption;

class UpdateKeysHandler
{
    public function handle(UpdateKeys $newMessage)
    {
        $actor = $newMessage->actor;
        $data = $newMessage->data;
        $newEncryption = Encryption::where('user_id', $actor->id)->firstOrFail();
        $newEncryption->prekeys = json_encode($data['preKeys']);
        $newEncryption->prekey_index = 0;
        $newEncryption->bundleProto = $data['bundle'];
        $newEncryption->identity_key = $data['encryptedIdentity'];
        $newEncryption->save();
        return $newEncryption;
    }
}