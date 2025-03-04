<?php
namespace App\Controllers;

use Firebase\JWT\JWT;
use App\Validation\LoginValidation; // Inclure la classe de validation
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    private $loginValidation;

    public function __construct(LoginValidation $loginValidation)
    {
        $this->loginValidation = $loginValidation;
    }

    public function login(Request $request, Response $response): Response
    {
        // Lire manuellement le corps de la requête
        $rawBody = (string) $request->getBody();
        $data = json_decode($rawBody, true);
    
        // Vérifier si le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            $response->getBody()->write(json_encode(['error' => 'Invalid JSON']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    
        // Vérifier si les données sont bien reçues
        if (empty($data)) {
            $response->getBody()->write(json_encode(['error' => 'No data received']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    
        // Valider les données avec la classe LoginValidation
        $validation = $this->loginValidation->validate($data);
    
        if ($validation->fails()) {
            $response->getBody()->write(json_encode(['errors' => $validation->errors()]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    
        // Comparer les informations avec des valeurs hardcodées
        if ($data['username'] !== 'admin' || $data['password'] !== 'secret') {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    
        // Générer un token JWT
        $payload = [
            'username' => $data['username'],
            'iat' => time(),
        ];
    
        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    
        // Retourner le token JWT
        $response->getBody()->write(json_encode(['token' => $jwt]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
