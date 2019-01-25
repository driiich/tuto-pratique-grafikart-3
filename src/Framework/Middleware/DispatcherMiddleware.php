<?php

namespace Framework\Middleware;

use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DispatcherMiddleware
{
    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * RouterMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return Response|mixed
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Router\Route::class);
        if (is_null($route)) {
            return $next($request);
        }
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        # Exécute le callback (PostCrudAction par exemple avec méthode __invoke) précédemment stocké dans la variable Request par le Router
        # et renvoi la vue avec les paramètres, envoyée à présent dans la Response
        $response = call_user_func_array($callback, [$request]);

        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is not a string or an instance of ResponseInterface');
        }
    }
}
