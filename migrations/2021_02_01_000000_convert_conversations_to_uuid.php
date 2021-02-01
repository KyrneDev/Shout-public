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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Ramsey\Uuid\Uuid;

return [
    'up' => function (Builder $schema) {
        $schema->table('messages', function (Blueprint $table) {
            $table->string( 'id')->change();
            $table->string('conversation_id')->change();
        });

        $schema->table('conversation_user', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
        });

        $schema->table('conversations', function (Blueprint $table) {
            $table->renameColumn( 'id', 'oldId');
        });

        $schema->table('conversation_user', function (Blueprint $table) {
            $table->string( 'conversation_id')->change();
        });

        $schema->table('conversations', function (Blueprint $table) {
            $table->string('id')->index();
        });

        $conversations = \Kyrne\Shout\Conversation::all();

        foreach ($conversations as $conversation) {
            $conversation->id = Uuid::uuid4()->toString();
            \Kyrne\Shout\Message::where('conversation_id', $conversation->oldId)->update(['conversation_id' => $conversation->id]);
            \Kyrne\Shout\ConversationUser::where('conversation_id', $conversation->oldId)->update(['conversation_id' => $conversation->id]);
            $conversation->save();
            if (\Kyrne\Shout\Conversation::where('id', $conversation->id)->count() > 1) {
                \Kyrne\Shout\Conversation::where('id', $conversation->id)
                    ->orderBy('total_messages', 'asc')
                    ->take(1)
                    ->delete();
            }
        }


        $messages = \Kyrne\Shout\Message::all();

        foreach ($messages as $message) {
            $message->id = Uuid::uuid4()->toString();
            $message->save();
        }

        $schema->table('conversations', function (Blueprint $table) {
            $table->dropColumn( 'oldId');
            $table->primary('id');
        });

        $schema->table('messages', function (Blueprint $table) {
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });

        $schema->table('conversation_user', function (Blueprint $table) {
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
    },
    'down' => function (Builder $schema) {
    }
];
