<?php
/**
 * Make PSR7 Router's PSR15 Middleware friendly.
 */
namespace CodeJet\Roots;

use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use League\Route\RouteCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddlewarinator implements ServerMiddlewareInterface
{
    /**
     * @var RouteCollection
     */
    protected $router;
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * RouterMiddlewarinator constructor.
     *
     * @param RouteCollection $router
     * @param ResponseInterface $response
     */
    public function __construct(RouteCollection $router, ResponseInterface $response)
    {
        $this->router = $router;
        $this->response = $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return $this->router->dispatch(
            $request,
            $this->response
        );
    }
}
