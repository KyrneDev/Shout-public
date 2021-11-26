<?php

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
    public $include = array('user');

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $conversationId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');
        $limit = $this->extractLimit($request);
        $offset = $request->getQueryParams()['offset'];
        $conversation = Conversation::find($conversationId);
        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException();
        }
        $messages = Message::where('conversation_id', $conversationId)->orderBy('created_at', 'desc')->skip($offset)->take($limit)->get();
        $encryptionKeyId = $actor->id;
        foreach ($messages as $response) {
            $response->message = json_decode($response->message)->{$encryptionKeyId};
        }
        return $messages;
    }
}