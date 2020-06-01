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


use Flarum\Api\Controller\AbstractCreateController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Kyrne\Shout\Api\Serializers\KeySerializer;
use Kyrne\Shout\Commands\SaveEncryptionKeys;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class SaveEncryptionKeysController extends AbstractCreateController
{

    public $serializer = KeySerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');

        return $this->bus->dispatch(
            new SaveEncryptionKeys($actor, $request->getParsedBody())
        );
    }
}