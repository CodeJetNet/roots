<?php
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class WheatGrassController
{
    public function sprout(RequestInterface $request, ResponseInterface $response)
    {
        $response->getBody()->write('<h1>Gluten baby!</h1>');
        return $response;
    }
}