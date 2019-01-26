<?php
namespace Framework\Router;

use Framework\Router;
use Psr\Container\ContainerInterface;

class RouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $cache = null;
        if ($_ENV['ENV'] === 'production') {
            $cache = 'tmp/routes';
        }
        return new Router($cache);
    }
}
