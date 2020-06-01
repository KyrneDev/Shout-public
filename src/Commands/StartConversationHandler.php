<?php

namespace Kyrne\Shout\Commands;


use Flarum\User\AssertPermissionTrait;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
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

        $conversationIds = Conversation::with('recipients')
            ->get()
            ->where('user_id', $actor->id)
            ->pluck('conversation_id')
            ->all();

        $oldConversation = null;

        foreach ($conversationIds as $id) {
            $conversation = conversation::find($id);

            /**die(var_dump($conversation
                ->recipients()
                ->pluck('user_id')
                ->all()));**/

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

        foreach (array_merge([$actor->id], (array) $data['attributes']['recipient']) as $recipientId) {
            $recipient = new ConversationUser();
            $recipient->conversation_id = $conversation->id;
            $recipient->user_id = $recipientId;

            if (intval($recipientId) === intval($actor->id)) {
                $recipient->cipher = $data['attributes']['cipher'];
            }

            $recipient->save();
        }

        Encryption::where('user_id', $data['attributes']['recipient'])
            ->increment('prekey_index');

        return $conversation;
    }
}