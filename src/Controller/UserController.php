<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment as Twig;

class UserController extends BaseController
{
    protected $userRepository;

    public function __construct(Response $response, Twig $twig, UserRepository $userRepository)
    {
        parent::__construct($response, $twig);
        $this->userRepository = $userRepository;
    }

    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    public function create(Request $request): Response
    {
        $this->userRepository->logQueries();
        $post = $request->getParsedBody();

        $keys = ['name'];
        $fields = array_intersect_key($post['user'] ?: [], array_flip($keys));
        $fields['date_created'] = date('Y-m-d H:i:s');

        $new_user_id = '';
        $db_error = '';
        try {
            $new_user = $this->userRepository->create($fields);
            $new_user_id = $new_user->id;
        } catch (\Throwable $t) {
            $db_error = $t->getMessage();
        }

        return $this->render('users/index.html.twig', [
            'used_connections' => $this->userRepository->getUsedConnections(),
            'db_error' => $db_error,
            'user' => $fields,
            'new_user_id' => $new_user_id,
        ]);
    }
}
