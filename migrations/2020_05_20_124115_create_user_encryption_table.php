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

Use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('user_encryption_keys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->longText('bundle_proto');
            $table->longText('identity_key');
            $table->longText('prekeys');
            $table->integer('prekey_index')->default(0);
            $table->boolean('prekeys_exhausted');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    },


    'down' => function (Builder $schema) {
        $schema->dropIfExists('user_encryption_keys');
    }
];
