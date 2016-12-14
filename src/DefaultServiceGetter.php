<?php
/**
 * Getters that setup default services if one isn't already
 * configured in the container.
 */

namespace CodeJet\Roots;

trait DefaultServiceGetter
{
    /**
     * @return \League\Route\RouteCollectionInterface
     */
    public function getRouter()
    {
        if (!$this->container->has('router')) {
            $router = new \League\Route\RouteCollection($this->container);
            $this->container->share('router', $router);
        }

        return $this->container->get('router');
    }

    /**
     * @return \Zend\Diactoros\Response\SapiEmitter
     */
    public function getEmitter()
    {
        if (!$this->container->has('emitter')) {
            $this->container->share('emitter', \Zend\Diactoros\Response\SapiEmitter::class);
        }

        return $this->container->get('emitter');
    }

    /**
     * @return @return \Psr\Http\Message\RequestInterface
     */
    public function getPsrRequest()
    {
        if (!$this->container->has('request')) {
            $this->container->share('request', function () {
                return \Zend\Diactoros\ServerRequestFactory::fromGlobals(
                    $_SERVER,
                    $_GET,
                    $_POST,
                    $_COOKIE,
                    $_FILES
                );
            });
        }

        return $this->container->get('request');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getPsrResponse()
    {
        if (!$this->container->has('response')) {
            $this->container->share('response', \Zend\Diactoros\Response::class);
        }

        return $this->container->get('response');
    }
}
