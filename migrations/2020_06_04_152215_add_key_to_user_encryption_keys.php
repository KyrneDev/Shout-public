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
        $schema->table('user_encryption_keys', function (Blueprint $table) {
            $table->addColumn('string', 'key', ['length' => 100]);
        });
    },
    'down' => function (Builder $schema) {
        if ($schema->hasColumn('user_encryption_keys', 'key')) {
            $schema->table('user_encryption_keys', function (Blueprint $table) {
                $table->removeColumn( 'key');
            });
        }
    }
];