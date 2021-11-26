<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\AccessToken;
use Illuminate\Contracts\Bus\Dispatcher;
use Kyrne\Aegis\Http\SessionAuthenticator;
use Kyrne\Shout\Api\Serializers\KeySerializer;
use Kyrne\Shout\Commands\SaveEncryptionKeys;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class SaveEncryptionKeysController extends AbstractCreateController
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
        return $this->bus->dispatch(new SaveEncryptionKeys($actor, $request->getParsedBody()));
    }
}