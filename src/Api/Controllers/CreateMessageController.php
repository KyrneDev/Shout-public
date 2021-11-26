<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Kyrne\Shout\Api\Serializers\MessageSerializer;
use Kyrne\Shout\Commands\NewMessage;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateMessageController extends AbstractCreateController
{
    public $serializer = MessageSerializer::class;
    public $include = array('user');
    protected $bus;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->bus = $dispatcher;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $response = $this->bus->dispatch(new NewMessage($actor, Arr::get($request->getParsedBody(), 'data', array())));
        return $response;
    }
}