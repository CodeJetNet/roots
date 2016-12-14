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
        $container->share('emitter',$mockEmitter);

        $app = new Application($container);

        $app->addRoute('GET', '/', function ($req, $resp) use ($expectedOutputText) {
            $resp->getBody()->write($expectedOutputText);
        });

        $app->outputResponse();
    }

    protected function getApplication()
    {
        $container = new Container;

        return new Application($container);
    }
}
