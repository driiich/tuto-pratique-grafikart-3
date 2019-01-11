<?php
/**
 * Created by PhpStorm.
 * User: Cyril
 * Date: 06/01/2019
 * Time: 18:43
 */

namespace App\Framework\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareApp implements MiddlewareInterface
{
    private $callable;

    /**
     * MiddlewareApp constructor.
     * @param string|callable $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }
}
