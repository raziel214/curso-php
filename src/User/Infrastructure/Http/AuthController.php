<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http;

use App\Shared\Infrastructure\Http\{Controller, Session};
use App\Shared\Infrastructure\View\TwigRenderer;
use App\User\Application\AuthenticateUser;
use App\User\Domain\InvalidCredentials;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AuthController extends Controller
{
    public function __construct(
        TwigRenderer $view,
        Session $session,
        private readonly AuthenticateUser $authenticate,
    ) {
        parent::__construct($view, $session);
    }

    public function showLogin(Request $request): ResponseInterface
    {
        if ($this->session->userId() !== null) {
            return $this->redirect('/admin');
        }

        return $this->render('auth/login.twig', ['email' => '', 'error' => null]);
    }

    public function login(Request $request): ResponseInterface
    {
        $data = $this->body($request);
        $email = (string) ($data['email'] ?? '');

        try {
            $user = $this->authenticate->execute($email, (string) ($data['password'] ?? ''));
        } catch (InvalidCredentials $e) {
            return $this->render('auth/login.twig', ['email' => $email, 'error' => $e->getMessage()], 401);
        }

        $this->session->regenerate(); // evita fijación de sesión
        $this->session->set('user_id', $user->id());
        $this->session->set('user_email', $user->email());

        return $this->redirect('/admin');
    }

    public function logout(Request $request): ResponseInterface
    {
        $this->session->destroy();

        return $this->redirect('/login');
    }
}
