<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $schema->table('users', function (Blueprint $table) {
            $table->addColumn('boolean', 'PMSetup');
            $table->addColumn('integer', 'unread_messages');
        });
    },
    'down' => function (Builder $schema) {
        if ($schema->hasColumn('users', 'PMSetup')) {
            $schema->table('users', function (Blueprint $table) {
                $table->removeColumn( 'PMSetup');
                $table->removeColumn( 'unread_messages');
            });
        }
    }
];