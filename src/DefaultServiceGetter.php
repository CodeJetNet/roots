<?php
/**
 * Getters that setup default services if one isn't already
 * configured in the container.
 */

namespace CodeJet\Roots;

use CodeJet\Roots\Exception\ApplicationException;

trait DefaultServiceGetter
{
    /**
     * @return array
     * @throws ApplicationException
     */
    public function getMiddlewareStack()
    {
        if (!$this->container->has('middlewareStack')) {
            return [];
        }

        $middlewareStack = $this->container->get('middlewareStack');

        if (!is_array($middlewareStack)) {
            throw new ApplicationException('The Middleware Stack must be an array.');
        }

        return $middlewareStack;
    }

    /**
     * @return \League\Route\RouteCollection
     * @throws ApplicationException
     */
    public function getRouter()
    {
        if (!$this->container->has('router')) {
            throw new ApplicationException('A Router must be provided.');
        }

        return $this->container->get('router');
    }

    /**
     * @return \Zend\Diactoros\Response\SapiEmitter
     */
    public function getEmitter()
    {
        if (!$this->container->has('emitter')) {
            return new \Zend\Diactoros\Response\SapiEmitter();
        }

        return $this->container->get('emitter');
    }

    /**
     * @return @return \Psr\Http\Message\RequestInterface
     */
    public function getPsrRequest()
    {
        if (!$this->container->has('request')) {
            return \Zend\Diactoros\ServerRequestFactory::fromGlobals();
        }

        return $this->container->get('request');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getPsrResponse()
    {
        if (!$this->container->has('response')) {
            return new \Zend\Diactoros\Response();
        }

        return $this->container->get('response');
    }
}
