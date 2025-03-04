<?php
namespace App\Middleware;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            $response = new \Laminas\Diactoros\Response();
            $response->getBody()->write(json_encode(['error' => 'Token not provided']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            // Décoder le JWT avec la clé secrète et l'algorithme
            $decoded = JWT::decode($token, $_ENV['JWT_SECRET'], ['HS256']);
            $request = $request->withAttribute('decoded_token', $decoded);
            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new \Laminas\Diactoros\Response();
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }


}
