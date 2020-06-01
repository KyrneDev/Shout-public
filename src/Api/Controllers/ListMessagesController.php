<?php
/**
 *
 *  This file is part of kyrne/shout
 *
 *  Copyright (c) 2020 Kyrne.
 *
 *  For the full copyright and license information, please view the license.md
 *  file that was distributed with this source code.
 *
 */

namespace Kyrne\Shout\Api\Controllers;


use Flarum\Api\Controller\AbstractListController;
use Kyrne\Shout\Api\Serializers\MessageSerializer;
use Tobscure\JsonApi\Document;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\Message;
use Psr\Http\Message\ServerRequestInterface;

class ListMessagesController extends AbstractListController
{
    public $serializer = MessageSerializer::class;

    public $include = ['user'];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $conversationId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');
        $limit = $this->extractLimit($request);
        $offset = $request->getQueryParams()['offset'];

        $conversation = Conversation::find($conversationId);

        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException;
        }

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        $id = $actor->id;

        foreach ($messages as $message) {
            $message->message = json_decode($message->message)->$id;
        }

        return $messages;
    }
}