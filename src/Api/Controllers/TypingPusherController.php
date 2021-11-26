<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\BasicUserSerializer;
use Illuminate\Contracts\Bus\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\Pusher;

class TypingPusherController extends AbstractShowController
{
    public $serializer = BasicUserSerializer::class;
    protected $pusher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->pusher = $dispatcher;
    }

    public function data(ServerRequestInterface $request, Document $document)
    {
        $data = $request->getParsedBody();
        if (resolve()->bound(Pusher::class)) {
            resolve(Pusher::class)->trigger('private-user' . $data['userId'], 'typing', array('conversationId' => $data['conversationId']));
        }
        return true;
    }
}