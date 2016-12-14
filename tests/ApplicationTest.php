<?php

namespace CodeJet\Roots;

use League\Container\Container;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRouteReturnsRoute()
    {
        $app = $this->getApplication();

        $route = $app->addRoute('GET', '/', function ($req, $resp) {
            return $resp;
        });

        $this->assertInstanceOf(\League\Route\Route::class, $route);
    }

    public function testResponseIsPSRResponseInterface()
    {
        $app = $this->getApplication();
        $app->addRoute('GET', '/', function ($req, $resp) {
            return $resp;
        });

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $app->getResponse());
    }

    /**
     * Not a fan of this...
     * Headers already sent due to PHPUnit outputting data for the tests.
     * So this (currently) fails. I imagine mocking the emitter is an easy way around this.
     *
     * @expectedException \RuntimeException
     */
    public function testResponseOutputBodyContentFailsHeaderAlreadySent()
    {
        $app = $this->getApplication();
        $app->addRoute('GET', '/', function ($req, $resp) {
            $resp->getBody()->write('This will surely fail.');
        });

        $app->outputResponse();
    }

    /**
     * Sooo...  We mock the emitter and with the mock, ensure the stuff emitter is getting is correct.
     */
    public function testResponseGetsPassedToEmitter()
    {
        $expectedOutputText = "body output text.";

        $mockEmitter = $this->getMockBuilder(\Zend\Diactoros\Response\SapiEmitter::class)
                            ->setMethods(['emit'])
                            ->disableOriginalConstructor()
                            ->getMock();

        $mockEmitter
            ->expects($this->once())
            ->method('emit')
            ->with($this->isInstanceOf(\Psr\Http\Message\ResponseInterface::class));

        $container = new Container();
        $container->share('emitter', $mockEmitter);

        $app = new Application($container);

        $app->addRoute('GET', '/', function ($req, $resp) use ($expectedOutputText) {
            $resp->getBody()->write($expectedOutputText);
        });

        $app->outputResponse();
    }

    /**
     * Testing with the Guzzle PSR7 Response Implementation
     *
     * Roots defaults to using the Zend\Diactoros PSR7 Response implementation
     * but it should work with any response implementation.
     */
    public function testGuzzlePSR7ResponseImplementation()
    {
        $container = new Container();
        $container->share('response', new \GuzzleHttp\Psr7\Response());

        $expectedOutputText = "body output text.";

        $app = new Application($container);
        $app->addRoute('GET', '/', function ($req, $resp) use ($expectedOutputText) {
            $resp->getBody()->write($expectedOutputText);
        });

        $appResponse = $app->getResponse();

        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $appResponse);
        $this->assertSame($expectedOutputText, (string)$appResponse->getBody());
    }

    /**
     * Testing with the Guzzle PSR7 ServerRequest Implementation
     *
     * Roots defaults to the Zend\Diactoros PSR7 implementation but it should
     * work with any compatible implementation.
     */
    public function testGuzzlePSR7ServerRequestImplementation()
    {
        $container = new Container();
        $container->share('request',new \GuzzleHttp\Psr7\ServerRequest(
            'GET',
            'http://www.example.com/testpath',
            [],
            '',
            '1.1',
            $_SERVER
        ));

        $expectedOutputText = "body output text.";

        $app = new Application($container);
        $app->addRoute('GET', '/testpath', function ($req, $resp) use ($expectedOutputText) {
            $resp->getBody()->write($expectedOutputText);
        });

        $appResponse = $app->getResponse();

        $this->assertSame($expectedOutputText, (string)$appResponse->getBody());
    }

    protected function getApplication()
    {
        $container = new Container;

        return new Application($container);
    }
}
