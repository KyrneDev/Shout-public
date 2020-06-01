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

namespace Kyrne\Shout\Api\Controllers;


use Flarum\Api\Controller\AbstractShowController;
use Flarum\Api\Serializer\BasicUserSerializer;
use Flarum\User\Exception\PermissionDeniedException;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

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

        if (!$actor->checkPassword($data['password'])) {
            throw new PermissionDeniedException;
        } else {
            return $actor;
        }
    }
}