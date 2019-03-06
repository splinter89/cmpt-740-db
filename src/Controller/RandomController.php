<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\RandomQueryGenerator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment as Twig;

class RandomController extends BaseController
{
    private $randomQueryGenerator;

    public function __construct(Response $response, Twig $twig, RandomQueryGenerator $randomQueryGenerator)
    {
        parent::__construct($response, $twig);
        $this->randomQueryGenerator = $randomQueryGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $type = $request->getAttribute('type');
        switch ($type) {
            case 'insert':
                $details = $this->randomQueryGenerator->insert();
                break;

            case 'select':
                $details = $this->randomQueryGenerator->select();
                break;

            case 'update':
                $details = $this->randomQueryGenerator->update();
                break;

            default:
                $details = '';
                break;
        }
        $json = json_encode($details);

        return $this->response($json, 'application/json');
    }
}
