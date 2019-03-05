<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Twig\Environment as Twig;

abstract class BaseController
{
    private $response;
    private $twig;

    public function __construct(Response $response, Twig $twig)
    {
        $this->response = $response;
        $this->twig = $twig;
    }

    protected function response($content, $content_type = 'text/html'): Response
    {
        $response = $this->response->withHeader('Content-Type', $content_type);
        $response->getBody()->write($content);
        return $response;
    }

    protected function render(string $template, array $context = []): Response
    {
        $content = $this->twig->render($template, $context);
        return $this->response($content);
    }
}
