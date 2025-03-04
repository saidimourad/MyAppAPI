<?php
namespace App\Controllers;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProtectedController
{
    public function index(Request $request, Response $response): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['error' => 'Token not provided']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = str_replace('Bearer ', '', $authHeader);
        try {
            // Décoder le token JWT
            $decoded = JWT::decode($token, $_ENV['JWT_SECRET'], ['HS256']);

            // Accéder aux claims du token (propriétés de l'objet stdClass)
            $username = $decoded->username; // Assurez-vous que 'username' est bien une propriété du token

            $response->getBody()->write(json_encode(['message' => 'Welcome ' . $username]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    } 

}