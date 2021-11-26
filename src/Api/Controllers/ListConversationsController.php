<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Kyrne\Shout\Api\Serializers\ConversationSerializer;
use Kyrne\Shout\Conversation;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListConversationsController extends AbstractListController
{
    public $serializer = ConversationSerializer::class;
    public $include = array('recipients', 'recipients.user');
    public $limit = 12;
    public $maxLimit = 12;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $limit = $this->extractLimit($request);
        $offset = array_key_exists('offset', $request->getQueryParams()) ? $request->getQueryParams()['offset'] : 0;
        $conversations = Conversation::whereHas('recipients', function ($conversation) use ($actor) {
            $conversation->where('user_id', $actor->id);
        })->orderBy('updated_at', 'desc')->skip($offset)->take($limit)->get();
        foreach ($conversations as $conversation) {
            foreach ($conversation->recipients as $recipient) {
                if ($recipient->user_id != $actor->id) {
                    $recipient->cipher = '';
                }
            }
        }
        return $conversations;
    }
}