<?php

Use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total_messages');
            $table->timestamps();
        });
    },


    'down' => function (Builder $schema) {
        $schema->dropIfExists('conversations');
    }
];
