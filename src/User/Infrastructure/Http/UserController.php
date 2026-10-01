<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Http;

use App\Shared\Domain\ValidationException;
use App\Shared\Infrastructure\Http\{Controller, Session};
use App\Shared\Infrastructure\View\TwigRenderer;
use App\User\Application\{CreateUser, DeleteUser, GetUser, ListUsers, UpdateUser, UserInput};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class UserController extends Controller
{
    public function __construct(
        TwigRenderer $view,
        Session $session,
        private readonly ListUsers $listUsers,
        private readonly GetUser $getUser,
        private readonly CreateUser $createUser,
        private readonly UpdateUser $updateUser,
        private readonly DeleteUser $deleteUser,
    ) {
        parent::__construct($view, $session);
    }

    public function index(Request $request): ResponseInterface
    {
        return $this->render('users/index.twig', [
            'users' => $this->listUsers->execute(),
            'currentUserId' => $this->session->userId(),
        ]);
    }

    public function create(Request $request): ResponseInterface
    {
        return $this->form(null, ['email' => '']);
    }

    public function store(Request $request): ResponseInterface
    {
        $data = $this->body($request);
        try {
            $this->createUser->execute(UserInput::fromArray($data));
        } catch (ValidationException $e) {
            return $this->form(null, ['email' => $data['email'] ?? ''], $e->errors());
        }

        return $this->redirect('/admin/users', 'Usuario creado.');
    }

    public function edit(Request $request): ResponseInterface
    {
        $user = $this->getUser->execute($this->id($request));

        return $this->form($user->id(), ['email' => $user->email()]);
    }

    public function update(Request $request): ResponseInterface
    {
        $id = $this->id($request);
        $data = $this->body($request);
        try {
            $user = $this->updateUser->execute($id, UserInput::fromArray($data));
        } catch (ValidationException $e) {
            return $this->form($id, ['email' => $data['email'] ?? ''], $e->errors());
        }

        if ($id === $this->session->userId()) {
            $this->session->set('user_email', $user->email());
        }

        return $this->redirect('/admin/users', 'Usuario actualizado.');
    }

    public function destroy(Request $request): ResponseInterface
    {
        try {
            $this->deleteUser->execute($this->id($request), $this->session->userId());
        } catch (ValidationException $e) {
            return $this->redirect('/admin/users', $e->getMessage(), 'danger');
        }

        return $this->redirect('/admin/users', 'Usuario eliminado.');
    }

    /** @param array<string, mixed> $values @param array<string, string> $errors */
    private function form(?int $id, array $values, array $errors = []): ResponseInterface
    {
        return $this->render('users/form.twig', compact('id', 'values', 'errors'), $errors ? 422 : 200);
    }
}
