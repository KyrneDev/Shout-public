<?php

namespace Kyrne\Shout\Api\Controllers;

use Kyrne\Shout\Api\Serializers\ConversationSerializer;
use Flarum\Api\Controller\AbstractCreateController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Kyrne\Shout\Commands\StartConversation;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateConversationController extends AbstractCreateController
{
    public $serializer = ConversationSerializer::class;
    public $include = array('messages', 'recipients', 'recipients.user');
    protected $bus;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->bus = $dispatcher;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $response = $this->bus->dispatch(new StartConversation($actor, Arr::get($request->getParsedBody(), 'data', array())));
        return $response;
    }
}