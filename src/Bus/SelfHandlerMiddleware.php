<?php

namespace Isaac\Bus;

use Isaac\Services\ContainerAwareTrait;
use League\Container\ContainerInterface;
use League\Tactician\Middleware;

/**
 * A command bus middleware that allows commands
 * to handle themselves with injected dependencies.
 */
class SelfHandlerMiddleware implements Middleware
{
    use ContainerAwareTrait;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if (!method_exists($command, 'handle')) {
            return $next($command);
        }

        return $this->container->call([$command, 'handle']);
    }
}
