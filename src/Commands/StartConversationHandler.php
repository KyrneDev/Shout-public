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

namespace Kyrne\Shout\Commands;


use Flarum\User\AssertPermissionTrait;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use InvalidArgumentException;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Encryption;

class StartConversationHandler
{
    use AssertPermissionTrait;

    /**
     * @var BusDispatcher
     */
    protected $bus;

    public function __construct(BusDispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function handle(StartConversation $command)
    {
        $actor = $command->actor;
        $data = $command->data;

        $this->assertCan($actor, 'startConversation');

        if (intval($data['attributes']['recipient']) === intval($actor->id)) {
            throw new InvalidArgumentException;
        }

        $conversationIds = ConversationUser::where('user_id', $actor->id)
            ->pluck('conversation_id')
            ->all();

        $oldConversation = null;

        foreach ($conversationIds as $id) {
            $conversation = conversation::find($id);

            if (in_array($data['attributes']['recipient'], $conversation
                ->recipients()
                ->pluck('user_id')
                ->all())) {
                $oldConversation = $conversation;
                break;
            }
        }

        if ($oldConversation) {
            $oldConversation->notNew = true;
            return $oldConversation;
        }

        $conversation = Conversation::start();

        // TODO validator

        $conversation->save();

        try {
            $this->bus->dispatch(
                new NewMessage($actor, $data, $conversation->id)
            );
        } catch (\Exception $e) {
            $conversation->delete;

            throw $e;
        }

        foreach (array_merge([$actor->id], (array)$data['attributes']['recipient']) as $recipientId) {
            $recipient = new ConversationUser();
            $recipient->conversation_id = $conversation->id;
            $recipient->user_id = $recipientId;

            if (intval($recipientId) === intval($actor->id)) {
                $recipient->cipher = $data['attributes']['cipher'];
            }

            $recipient->save();
        }

        $keys = Encryption::where('user_id', $data['attributes']['recipient'])
            ->first();

        $keys->increment('prekey_index');

        if ($keys->prekey_index > 47) {
            $keys->prekeys_exhausted = true;
        }

        $keys->save();

        return $conversation;
    }
}