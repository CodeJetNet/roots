<?php

namespace CodeJet\Roots;

use Equip\Dispatch\MiddlewarePipe;
use Interop\Container\ContainerInterface;

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

        // Bootstrap the app with configuration..
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
        $routerMiddleware = new RouterMiddlewarinator(
            $this->getRouter(),
            $this->getPsrResponse()
        );

        $middleware = $this->getMiddlewareStack();

        array_push($middleware, $routerMiddleware);

        $pipe = new MiddlewarePipe($middleware);

        return $pipe->dispatch(
            $this->getPsrRequest(),
            function () {
                throw new \Exception('Our tree has no leaves.. and subsequently died from lack of middleware.');
            }
        );
    }

    /**
     * Output the response stream (aka: display the content)
     */
    public function outputResponse()
    {
        $this->getEmitter()->emit($this->getResponse());
    }
}
