<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\NotFoundException;
use App\Shared\Infrastructure\View\TwigRenderer;
use Aura\Router\RouterContainer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Front controller: enruta, aplica autenticación y CSRF, y traduce
 * excepciones de dominio a respuestas HTTP.
 */
final class Kernel
{
    public function __construct(
        private readonly RouterContainer $router,
        private readonly ContainerInterface $container,
        private readonly Session $session,
        private readonly TwigRenderer $view,
        private readonly bool $debug = false,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->start();

        try {
            $route = $this->router->getMatcher()->match($request);
            if ($route === false) {
                return $this->error(404, 'Página no encontrada.');
            }

            if ($route->auth === true && $this->session->userId() === null) {
                return new RedirectResponse('/login');
            }

            if ($request->getMethod() === 'POST') {
                $body = $request->getParsedBody();
                if (!$this->session->isValidCsrf(is_array($body) ? ($body['_csrf'] ?? null) : null)) {
                    return $this->error(419, 'La sesión expiró. Recarga la página e intenta de nuevo.');
                }
            }

            foreach ($route->attributes as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            [$class, $method] = $route->handler;

            return $this->container->get($class)->$method($request);
        } catch (NotFoundException $e) {
            return $this->error(404, $e->getMessage());
        } catch (\Throwable $e) {
            error_log((string) $e);

            return $this->error(500, $this->debug ? $e->getMessage() : 'Error interno del servidor.');
        }
    }

    private function error(int $status, string $message): ResponseInterface
    {
        return new HtmlResponse(
            $this->view->render('errors/error.twig', ['status' => $status, 'message' => $message]),
            $status,
        );
    }
}
