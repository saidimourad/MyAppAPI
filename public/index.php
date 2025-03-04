<?php
require_once __DIR__ . '/../app/bootstrap.php';

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Server\RequestHandlerInterface;
use App\Middleware\AuthMiddleware;
use App\Middleware\LoggingMiddleware;

// Charger les routes
$routes = require __DIR__ . '/../config/routes.php';

// Créer le dispatcher FastRoute
$dispatcher = simpleDispatcher(function (RouteCollector $r) use ($routes) {
    foreach ($routes['routes'] as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

// Créer la requête HTTP à partir des variables globales
$request = ServerRequestFactory::fromGlobals();

// Dispatcher la route selon la méthode HTTP et l'URL
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $response = new Response();
        $response->getBody()->write('404 Not Found');
        $response = $response->withStatus(404);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response();
        $response->getBody()->write('405 Method Not Allowed');
        $response = $response->withStatus(405);
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Récupérer le contrôleur depuis le conteneur
        $controller = $container->get($handler[0]);
        $method = $handler[1];

        // Créer un RequestHandler pour le contrôleur
        $requestHandler = new class($controller, $method, $vars) implements RequestHandlerInterface {
            private $controller;
            private $method;
            private $vars;

            public function __construct($controller, $method, $vars)
            {
                $this->controller = $controller;
                $this->method = $method;
                $this->vars = $vars;
            }

            public function handle(ServerRequestInterface $request): Response
            {
                // Appeler la méthode du contrôleur et retourner la réponse
                return $this->controller->{$this->method}($request, new Response(), $this->vars);
            }
        };

        // Vérification que les middlewares existent pour la route et s'ils sont valides
        $middlewareQueue = [];

        if (isset($route[3]) && is_array($route[3])) {
            // Ajouter le middleware d'authentification si nécessaire
            if (in_array(AuthMiddleware::class, $route[3])) {
                $middlewareQueue[] = $container->get(AuthMiddleware::class);
            }

            // Ajouter le middleware de logging
            if (in_array(LoggingMiddleware::class, $route[3])) {
                $middlewareQueue[] = $container->get(LoggingMiddleware::class);
            }
        }

        // Appliquer les middlewares dans la queue
        foreach ($middlewareQueue as $middleware) {
            $request = $middleware->process($request, $requestHandler);
        }

        // Appeler la méthode du contrôleur (si tous les middlewares ont été exécutés)
        $response = $requestHandler->handle($request);
        break;
}

// Envoyer la réponse
(new SapiEmitter())->emit($response);
