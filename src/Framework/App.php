<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App implements DelegateInterface
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];
    /**
     * @var string
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * App constructor.
     * @param string $definition
     */
    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * @param string $middleware
     * @return $this
     */
    public function pipe(string $middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('Aucun middleware n\'a intercepté cette requête');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        return call_user_func_array($middleware, [$request, [$this, 'process']]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        # Initialise les modules
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        # Lance les middlewares
        return $this->process($request);
    }

    /**
     * @return ContainerInterface
     * @throws \Exception
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new \DI\ContainerBuilder();
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
