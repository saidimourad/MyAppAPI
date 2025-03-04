<?php
namespace App\Controllers;
use App\Services\UuidService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UuidController
{
    private $uuidService;

    // Injection du service UuidService via le constructeur
    public function __construct(UuidService $uuidService)
    {
        $this->uuidService = $uuidService;
    }

    public function generate(Request $request, Response $response, array $args): Response
    {
        // Utilisation du service pour générer le UUID
        $uuid = $this->uuidService->generateUuid();

        // Retourner le UUID dans la réponse
        $response->getBody()->write(json_encode(['uuid' => $uuid]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
