<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\BasicUserSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Pusher\Pusher;

class TypingPusherController extends AbstractShowController
{
    public $serializer = BasicUserSerializer::class;

    public function data(ServerRequestInterface $request, Document $document)
    {
        $data = $request->getParsedBody();

        if (app()->bound(Pusher::class)) {
            app(Pusher::class)->trigger('private-user' . $data['userId'], 'typing', []);
        }

        return true;
    }
}