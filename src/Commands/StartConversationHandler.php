<?php

namespace Kyrne\Shout\Commands;

use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use InvalidArgumentException;
use Kyrne\Shout\Conversation;
use Kyrne\Shout\ConversationUser;
use Kyrne\Shout\Encryption;

class StartConversationHandler
{
    protected $bus;

    public function __construct(BusDispatcher $dispatcher)
    {
        $this->bus = $dispatcher;
    }

    public function handle(StartConversation $newMessage)
    {
        $actor = $newMessage->actor;
        $data = $newMessage->data;
        $actor->assertCan('startConversation');
        if (intval($data['attributes']['recipient']) === intval($actor->id)) {
            throw new InvalidArgumentException();
        }
        $sp735589 = ConversationUser::where('user_id', $actor->id)->pluck('conversation_id')->all();
        $sp3c85ab = null;
        foreach ($sp735589 as $encryptionKeyId) {
            $conversation = Conversation::find($encryptionKeyId);
            if (in_array($data['attributes']['recipient'], $conversation->recipients()->pluck('user_id')->all())) {
                $sp3c85ab = $conversation;
                break;
            }
        }
        if ($sp3c85ab) {
            $sp3c85ab->notNew = true;
            return $sp3c85ab;
        }
        $conversation = Conversation::start();
        $conversation->save();
        foreach (array_merge(array($actor->id), (array)$data['attributes']['recipient']) as $sp5789a3) {
            $recipient = new ConversationUser();
            $recipient->conversation_id = $conversation->id;
            $recipient->user_id = $sp5789a3;
            if (intval($sp5789a3) === intval($actor->id)) {
                $recipient->cipher = $data['attributes']['cipher'];
            }
            $recipient->save();
        }
        try {
            $this->bus->dispatch(new NewMessage($actor, $data, $conversation->id));
        } catch (\Exception $spf96ebf) {
            $conversation->delete;
            throw $spf96ebf;
        }
        $newEncryption = Encryption::where('user_id', $data['attributes']['recipient'])->first();
        $newEncryption->increment('prekey_index');
        if ($newEncryption->prekey_index > 47) {
            $newEncryption->prekeys_exhausted = true;
        }
        $newEncryption->save();
        return $conversation;
    }
}