<?php

namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class MethodMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        // Traitement des méthodes de formulaire, en particulier la méthode DELETE non interprétée par les navigateurs
        $parseBody = $request->getParsedBody();
        if (array_key_exists('_method', $parseBody) &&
            in_array($parseBody['_method'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parseBody['_method']);
        }
        return $next($request);
    }
}
