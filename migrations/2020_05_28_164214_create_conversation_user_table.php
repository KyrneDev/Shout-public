<?php
/**
 *
 *  This file is part of reflar/gamification
 *
 *  Copyright (c) ReFlar.
 *
 *  http://reflar.io
 *
 *  For the full copyright and license information, please view the license.md
 *  file that was distributed with this source code.
 *
 */

Use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('conversation_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conversation_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->longText('cipher');

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    },


    'down' => function (Builder $schema) {
        $schema->dropIfExists('conversation_user');
    }
];
