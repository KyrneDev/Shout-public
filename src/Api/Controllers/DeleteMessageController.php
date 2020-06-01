<?php

namespace Kyrne\Shout\Api\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Kyrne\Shout\Commands\HideMessage;
use Psr\Http\Message\ServerRequestInterface;

class DeleteMessageController extends AbstractDeleteController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request)
    {
        $id = Arr::get($request->getQueryParams(), 'id');

        $actor = $request->getAttribute('actor');

        $this->bus->dispatch(
            new HideMessage($id, $actor)
        );
    }
}