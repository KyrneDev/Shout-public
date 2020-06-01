<?php
/**
 *
 *  This file is part of reflar/gamification
 *
 *  Copyright (c) ReFlar.
 *
 *  http://reflar.io
 *
 *  For the full copyright and license information, please view the license.md
 *  file that was distributed with this source code.
 *
 */

namespace Kyrne\Shout\Api\Controllers;


use Flarum\Api\Controller\AbstractShowController;
use Illuminate\Support\Arr;
use Kyrne\Shout\Api\Serializers\KeySerializer;
use Kyrne\Shout\Encryption;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class GetIdentityController extends AbstractShowController
{
    public $serializer = KeySerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $userId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');

        if (intval($userId) === intval($actor->id)) {
            return Encryption::where('user_id', $actor->id)->firstOrFail();
        } else {
            $keys = Encryption::where('user_id', $userId)->firstOrFail();
            $keys->identity_key = null;
            $keys->prekey = json_decode($keys->prekeys)[$keys->prekey_index];
            $keys->prekeys = null;
            return $keys;
        }
    }
}