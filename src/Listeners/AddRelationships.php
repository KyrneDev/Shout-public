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

namespace Kyrne\Shout\Listeners;

use Flarum\Api\Controller;
use Flarum\Api\Event\Serializing;
use Flarum\Api\Event\WillGetData;
use Flarum\Api\Serializer;
use Flarum\Event\GetApiRelationship;
use Flarum\Event\GetModelRelationship;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Kyrne\Shout\Api\Serializers\ConversationRecipientSerializer;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Encryption;

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
        if ($event->isRelationship(User::class, 'conversations')) {
            return $event->model->hasMany(ConversationUser::class, 'user_id');
        }
    }

    public function prepareApiAttributes(Serializing $event)
    {
        if ($event->isSerializer(Serializer\ForumSerializer::class)) {
            $event->attributes['canMessage'] = $event->actor->can('startConversation');
            $event->attributes['shoutOwnPassword'] = (bool) $this->settings->get('kyrne-shout.set_own_password');
        }
        if ($event->isSerializer(Serializer\BasicUserSerializer::class)) {
            $keys = Encryption::where('user_id', $event->model->id)->first();
            $event->attributes['PMSetup'] = (bool) $event->model->PMSetup;
            $event->attributes['PrekeysExhausted'] = (bool) $keys ? $keys->prekeys_exhausted : false;
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
        if ($event->isRelationship(Serializer\UserSerializer::class, 'conversations')) {
            return $event->serializer->hasMany($event->model, ConversationRecipientSerializer::class, 'conversations');
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
            || $event->isController(Controller\UpdateUserController::class)) {
            $event->addInclude([
                'conversations',
            ]);
        }
    }
}