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
    /**
     * @var string
     */
    public $serializer = ConversationSerializer::class;

    public $include = [
        'messages',
        'recipients',
        'recipients.user'
    ];

    /**
     * @var Dispatcher
     */
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');

        $conversation = $this->bus->dispatch(
            new StartConversation($actor, Arr::get($request->getParsedBody(), 'data', []))
        );

        return $conversation;
    }
}