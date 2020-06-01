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
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Kyrne\Shout\Api\Serializers\MessageSerializer;
use Kyrne\Shout\Commands\UpdateMessageEncryption;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateMessageEncryptionController extends AbstractShowController
{
    public $serializer = MessageSerializer::class;

    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');

        return $this->bus->dispatch(
            new UpdateMessageEncryption($id, $actor, $request->getParsedBody())
        );
    }
}