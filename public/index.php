<?php
/**
 * Bootstrap our app...
 * Most of this was gleamed from http://route.thephpleague.com/hello-world/
 */

// The Maestro.
require __DIR__ . '/../vendor/autoload.php';

// Mmm. Tasty tasty wheatgrass.
require __DIR__ . '/WheatGrassController.php';

/**
 * A container in which to plant things.
 */
$container = new League\Container\Container;

/**
 * Plant the seed.
 */
$app = new CodeJet\Roots\Application($container);

/**
 * Add some routes.
 */
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$app->addRoute('GET','/',function (ServerRequestInterface $request, ResponseInterface $response) {
    $response->getBody()->write('<h1>I am GROOOT!!</h1> <a href="/wheatgrass">Please meet wheatgrass.</a>');

    return $response;
});

$app->addRoute('GET','/wheatgrass','WheatGrassController::sprout');

/**
 * And sprout.
 */
$app->outputResponse();