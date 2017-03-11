<?php

namespace Isaac\Bus;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Tactician\CommandBus;

class CommandBusServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [CommandBus::class];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(CommandBus::class, function () {
            return new CommandBus([
                new SelfHandlerMiddleware($this->container),
            ]);
        });
    }
}
