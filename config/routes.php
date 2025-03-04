<?php
use App\Controllers\UuidController;
use App\Controllers\AuthController;
use App\Controllers\ProtectedController;
use App\Middleware\AuthMiddleware;
use App\Middleware\LoggingMiddleware;

return [
    'routes' => [
        ['GET', '/api/uuid', [UuidController::class, 'generate'], []],
        ['POST', '/api/login', [AuthController::class, 'login'], []],
        ['GET', '/api/protected', [ProtectedController::class, 'index'], [AuthMiddleware::class, LoggingMiddleware::class]],
    ],
];