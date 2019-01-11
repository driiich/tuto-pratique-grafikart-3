<?php
/**
 * Created by PhpStorm.
 * User: Cyril
 * Date: 04/01/2019
 * Time: 17:07
 */

namespace Framework\Router;

/**
 * Class Route
 * Represent a matched Route
 */
class Route
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $parameters;
    /**
     * @var callable
     */
    private $callback;

    public function __construct(string $name, callable $callback, array $parameters)
    {

        $this->name = $name;
        $this->parameters = $parameters;
        $this->callback = $callback;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
