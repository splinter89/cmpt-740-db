<?php

declare(strict_types=1);

namespace App;

use FastRoute\Dispatcher;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\TrailingSlash;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Container\ContainerInterface;
use Relay\Relay;
use Zend\Diactoros\ServerRequestFactory;

class App
{
    private $container;
    private $router;

    public function __construct(ContainerInterface $container, Dispatcher $router)
    {
        $this->container = $container;
        $this->router = $router;
    }

    public function run()
    {
        try {
            $middlewares = [
                (new TrailingSlash(true))->redirect(true),
                new FastRoute($this->router),
                new RequestHandler($this->container),
            ];
            $requestHandler = new Relay($middlewares);

            $request = ServerRequestFactory::fromGlobals();
            $response = $requestHandler->handle($request);

            $emitter = new SapiEmitter();
            $emitter->emit($response);
        } catch (\Throwable $t) {
            //error_log((string)$t);
            var_dump($t);
        }
    }
}
