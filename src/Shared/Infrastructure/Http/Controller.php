<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Infrastructure\View\TwigRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** Utilidades comunes de la capa de presentación. No contiene lógica de negocio. */
abstract class Controller
{
    public function __construct(
        protected readonly TwigRenderer $view,
        protected readonly Session $session,
    ) {
    }

    /** @param array<string, mixed> $data */
    protected function render(string $template, array $data = [], int $status = 200): ResponseInterface
    {
        return new HtmlResponse($this->view->render($template, $data), $status);
    }

    protected function redirect(string $to, ?string $flash = null, string $type = 'success'): ResponseInterface
    {
        if ($flash !== null) {
            $this->session->flash($type, $flash);
        }

        return new RedirectResponse($to);
    }

    /** @return array<string, mixed> */
    protected function body(ServerRequestInterface $request): array
    {
        $body = $request->getParsedBody();

        return is_array($body) ? $body : [];
    }

    protected function id(ServerRequestInterface $request): int
    {
        return (int) $request->getAttribute('id');
    }
}
