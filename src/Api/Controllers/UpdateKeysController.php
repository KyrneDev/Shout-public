<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\AccessToken;
use Illuminate\Contracts\Bus\Dispatcher;
use Kyrne\Shout\Api\Serializers\KeySerializer;
use Kyrne\Shout\Commands\UpdateKeys;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateKeysController extends AbstractCreateController
{
    public $serializer = KeySerializer::class;
    protected $bus;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->bus = $dispatcher;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        return $this->bus->dispatch(new UpdateKeys($actor, $request->getParsedBody()));
    }
}