<?php

namespace Kyrne\Shout\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Kyrne\Shout\Encryption;

class KeySerializer extends AbstractSerializer
{
    protected $type = 'encryption_keys';

    protected function getDefaultAttributes($newEncryption)
    {
        if (!$newEncryption instanceof Encryption) {
            throw new \InvalidArgumentException(get_class($this) . ' can only serialize instances of ' . Encryption::class);
        }
        return array('bundle' => $newEncryption->bundleProto, 'identityKey' => $newEncryption->identity_key, 'prekey' => $newEncryption->prekey, 'index' => $newEncryption->prekey_index, 'prekeysExhausted' => (bool)$newEncryption->prekeys_exhausted);
    }

    protected function user($newEncryption)
    {
        return $this->hasOne($newEncryption, BasicUserSerializer::class);
    }
}