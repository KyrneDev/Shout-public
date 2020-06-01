<?php


namespace Kyrne\Shout\Listeners;

use Flarum\Api\Controller;
use Flarum\Api\Event\Serializing;
use Flarum\Api\Event\WillGetData;
use Flarum\Api\Serializer;
use Flarum\Event\GetApiRelationship;
use Flarum\Event\GetModelRelationship;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use FoF\Gamification\Api\Controllers\OrderByPointsController;
use Illuminate\Contracts\Events\Dispatcher;
use Kyrne\Shout\Api\Serializers\ConversationSerializer;
use Kyrne\Shout\Conversation;

class AddRelationships
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(GetModelRelationship::class, [$this, 'getModelRelationship']);
        $events->listen(GetApiRelationship::class, [$this, 'getApiAttributes']);
        $events->listen(Serializing::class, [$this, 'prepareApiAttributes']);
        $events->listen(WillGetData::class, [$this, 'includeData']);
    }

    /**
     * @param GetModelRelationship $event
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getModelRelationship(GetModelRelationship $event)
    {
        if ($event->isRelationship(User::class, 'from_conversations')) {
            return $event->model->hasMany(Conversation::class, 'user_one_id');
        }
        if ($event->isRelationship(User::class, 'to_conversations')) {
            return $event->model->hasMany(Conversation::class, 'user_two_id');
        }
    }

    public function prepareApiAttributes(Serializing $event)
    {
        if ($event->isSerializer(Serializer\ForumSerializer::class)) {
            $event->attributes['canMessage'] = $event->actor->can('startConversation');
        }
        if ($event->isSerializer(Serializer\BasicUserSerializer::class)) {
            $event->attributes['PMSetup'] = (bool) $event->model->PMSetup;
        }
        if ($event->isSerializer(Serializer\UserSerializer::class)) {
            if ($event->model->id === $event->actor->id) {
                $event->attributes['unreadMessages'] = $event->model->unread_messages;
            }
        }
    }

    /**
     * @param GetApiRelationship $event
     *
     * @return \Tobscure\JsonApi\Relationship|null
     */
    public function getApiAttributes(GetApiRelationship $event)
    {
        if ($event->isRelationship(Serializer\UserSerializer::class, 'from_conversations')) {
            return $event->serializer->hasMany($event->model, ConversationSerializer::class, 'from_conversations');
        }
        if ($event->isRelationship(Serializer\UserSerializer::class, 'to_conversations')) {
            return $event->serializer->hasMany($event->model, ConversationSerializer::class, 'to_conversations');
        }
    }

    /**
     * @param WillGetData $event
     */
    public function includeData(WillGetData $event)
    {
        if ($event->isController(Controller\ListUsersController::class)
            || $event->isController(Controller\ShowUserController::class)
            || $event->isController(Controller\CreateUserController::class)
            || $event->isController(OrderByPointsController::class)
            || $event->isController(Controller\UpdateUserController::class)) {
            $event->addInclude([
                'from_conversations',
                'to_conversations',
                'to_conversations.fromUser',
                'to_conversations.toUser',
                'from_conversations.fromUser',
                'from_conversations.toUser'
            ]);
        }
    }
}