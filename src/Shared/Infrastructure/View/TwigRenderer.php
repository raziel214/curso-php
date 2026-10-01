<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\View;

use App\Shared\Infrastructure\Http\Session;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

final class TwigRenderer
{
    private Environment $twig;

    public function __construct(string $viewsPath, Session $session, bool $debug = false)
    {
        $this->twig = new Environment(new FilesystemLoader($viewsPath), [
            'debug' => $debug,
            'cache' => false,
            'autoescape' => 'html',
            'strict_variables' => $debug,
        ]);
        $this->twig->addFunction(new TwigFunction('csrf_token', fn () => $session->csrfToken()));
        $this->twig->addFunction(new TwigFunction('flashes', fn () => $session->consumeFlashes()));
        $this->twig->addFunction(new TwigFunction('current_user_email', fn () => $session->get('user_email')));
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }
}
