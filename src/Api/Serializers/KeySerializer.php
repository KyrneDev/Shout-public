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

namespace Kyrne\Shout\Api\Serializers;


use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;
use Kyrne\Shout\Encryption;

class KeySerializer extends AbstractSerializer
{
    /**
     * @var string
     */
    protected $type = 'encryption_keys';

    /**
     * @param $group
     *
     * @return array
     */
    protected function getDefaultAttributes($keys)
    {
        if (!($keys instanceof Encryption)) {
            throw new \InvalidArgumentException(
                get_class($this).' can only serialize instances of '.Encryption::class
            );
        }

        return [
            'bundle' => $keys->bundle_proto,
            'identityKey' => $keys->identity_key,
            'prekey' => $keys->prekey,
            'index' => $keys->prekey_index,
            'prekeysExhausted' => (bool) $keys->prekeys_exhausted
        ];
    }

    protected function user($keys)
    {
        return $this->hasOne($keys, BasicUserSerializer::class);
    }
}