<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kyrne\Shout\Api\Serializers\ConversationSerializer;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\Message;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowConversationController extends AbstractShowController
{
    public $serializer = ConversationSerializer::class;
    public $include = array('messages', 'recipients', 'recipients.user');

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $conversationId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');
        $sp077def = $this->extractInclude($request);
        $conversation = Conversation::findOrFail($conversationId);
        if (!$conversation->recipients()->where('user_id', $actor->id)->get()) {
            throw new PermissionDeniedException();
        }
        if (in_array('messages', $sp077def)) {
            $sp535980 = $this->speb9cb7($sp077def);
            $this->sp5fbd26($conversation, $request, $sp535980);
        }
        $conversation->load(array_filter($sp077def, function ($sp83a041) {
            return !Str::startsWith($sp83a041, 'messages');
        }));
        return $conversation;
    }

    private function sp5fbd26(Conversation $conversation, ServerRequestInterface $request, array $sp077def)
    {
        $limit = $this->extractLimit($request);
        $offset = $this->sp94dd6b($request, $conversation, $limit);
        $sp4c199c = $this->sp3e5f73($conversation);
        $sp78a22c = $this->sp4472ad($conversation, $offset, $limit, $sp077def);
        array_splice($sp4c199c, $offset, $limit, $sp78a22c);
        $conversation->setRelation('messages', $sp4c199c);
    }

    private function sp3e5f73(Conversation $conversation)
    {
        return $conversation->messages()->latest()->pluck('id')->all();
    }

    private function speb9cb7(array $sp077def)
    {
        $spe23da4 = strlen($spb9ee3f = 'posts.');
        $sp626653 = array();
        foreach ($sp077def as $sp83a041) {
            if (substr($sp83a041, 0, $spe23da4) === $spb9ee3f) {
                $sp626653[] = substr($sp83a041, $spe23da4);
            }
        }
        return $sp626653;
    }

    private function sp94dd6b(ServerRequestInterface $request, Conversation $conversation, $limit)
    {
        $spe9635d = $request->getQueryParams();
        $actor = $request->getAttribute('actor');
        if (($spa25a48 = Arr::get($spe9635d, 'page.near')) > 1) {
            $offset = message::getIndexForNumber($conversation->id, $spa25a48, $actor);
            $offset = max(0, $offset - $limit / 2);
        } else {
            $offset = $this->extractOffset($request);
        }
        return $offset;
    }

    private function sp4472ad($conversation, $offset, $limit, array $sp077def)
    {
        $spf7b294 = $conversation->messages();
        $spf7b294->latest()->skip($offset)->take($limit)->with($sp077def);
        $messages = $spf7b294->get()->all();
        foreach ($messages as $response) {
            $response->conversation = $conversation;
        }
        return $messages;
    }
}