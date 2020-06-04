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

namespace Kyrne\Shout\Api\Controllers;


use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Kyrne\Shout\Encryption;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Hashing\Hasher;

class VerifyPasswordController extends AbstractShowController
{
    public $serializer = BasicUserSerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');

        $data = $request->getParsedBody();

        if (app(SettingsRepositoryInterface::class)->get('kyrne-shout.set_own_password')) {
            $encryption = Encryption::where('user_id', $actor->id)->first();

            if ($encryption) {
                if (app(Hasher::class)->check($data['password'], $encryption->key)) {
                    return $actor;
                } else {
                    throw new PermissionDeniedException;
                }
            }
        } else {
            if (!$actor->checkPassword($data['password'])) {
                throw new PermissionDeniedException;
            } else {
                return $actor;
            }
        }
    }
}