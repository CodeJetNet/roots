<?php

namespace CodeJet\Roots;

use League\Container\ContainerInterface;

class Application
{
    use DefaultServiceGetter;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        // Now... to bootstrap the app with configuration..
        // Middleware, Routes, Services, etc...
    }

    /**
     * @param $method
     * @param $pattern
     * @param $callable
     *
     * @return \League\Route\Route
     */
    public function addRoute($method, $pattern, $callable)
    {
        $route = $this->getRouter()->map($method, $pattern, $callable);

        return $route;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        $response = $this->getRouter()->dispatch(
            $this->getPsrRequest(),
            $this->getPsrResponse()
        );

        return $response;
    }

    /**
     * Output the response stream (aka: display the content)
     */
    public function outputResponse()
    {
        $this->getEmitter()->emit($this->getResponse());
    }
}
