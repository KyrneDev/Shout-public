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