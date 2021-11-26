<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Kyrne\Shout\Commands\HideMessage;
use Psr\Http\Message\ServerRequestInterface;

class DeleteMessageController extends AbstractDeleteController
{
    protected $bus;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->bus = $dispatcher;
    }

    protected function delete(ServerRequestInterface $request)
    {
        $encryptionKeyId = Arr::get($request->getQueryParams(), 'id');
        $actor = $request->getAttribute('actor');
        $this->bus->dispatch(new HideMessage($encryptionKeyId, $actor));
    }
}