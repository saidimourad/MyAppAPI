<?php
namespace App\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Logguer l'URL et la méthode HTTP
        $this->logger->info('Requête reçue', [
            'method' => $request->getMethod(),
            'uri' => (string) $request->getUri(),
        ]);

        // Passe la requête au gestionnaire suivant
        return $handler->handle($request);
    }
}