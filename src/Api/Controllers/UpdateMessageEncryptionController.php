<?php

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

    public function __construct(Dispatcher $dispatcher)
    {
        $this->bus = $dispatcher;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $encryptionKeyId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');
        return $this->bus->dispatch(new UpdateMessageEncryption($encryptionKeyId, $actor, $request->getParsedBody()));
    }
}