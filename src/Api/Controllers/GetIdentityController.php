<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\AccessToken;
use Illuminate\Support\Arr;
use Kyrne\Shout\Api\Serializers\KeySerializer;
use Kyrne\Shout\Encryption;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class GetIdentityController extends AbstractShowController
{
    public $serializer = KeySerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $userId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');
        if (intval($userId) === intval($actor->id)) {
            return Encryption::where('user_id', $actor->id)->firstOrFail();
        } else {
            $newEncryption = Encryption::where('user_id', $userId)->firstOrFail();
            $newEncryption->identity_key = null;
            $newEncryption->prekey = json_decode($newEncryption->prekeys)[$newEncryption->prekey_index];
            $newEncryption->prekeys = null;
            return $newEncryption;
        }
    }
}