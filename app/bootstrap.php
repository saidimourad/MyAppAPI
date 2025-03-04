<?php
use App\Container\ServiceContainer;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use App\Services\UuidService;
use App\Controllers\AuthController;
use App\Controllers\UuidController;
use App\Validation\LoginValidation;
use App\Controllers\ProtectedController;
use App\Middleware\AuthMiddleware;
use App\Middleware\LoggingMiddleware;
use Dotenv\Dotenv;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configurer Whoops pour la gestion des erreurs en fonction de l'environnement

$env = $_ENV['APP_ENV'] ?? 'dev';  // Si $_ENV['APP_ENV'] n'est pas définie, 'dev' par défaut

if ($env === 'dev') {
      // Environnement de développement : activer Whoops pour afficher les erreurs de manière détaillée
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();
     } elseif ($env === 'prod') {
        // Environnement de production : désactiver l'affichage des erreurs et les enregistrer dans un fichier de log
        error_reporting(0);  // Désactive l'affichage des erreurs
        ini_set('log_errors', 1);  // Active la journalisation des erreurs
        ini_set('error_log', __DIR__ . '/../logs/error.log');  // Définit le fichier de log
 }

 // Créer une instance du conteneur de services
$container = new ServiceContainer();
// Enregistrer les services dans le conteneur
// Logger Monolog
$container->set(Logger::class, function () {
    $logger = new Logger('api');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../var/log/app.log', Logger::DEBUG));
    return $logger;
});

// Validateur Illuminate

$container->set(Validator::class, function () {
    $filesystem = new Filesystem();
    $loader = new FileLoader($filesystem, __DIR__ . '/../resources/lang');
    $translator = new Translator($loader, 'en');
    return new Validator($translator);
});

// Service UUID
$container->set(UuidService::class, function () {
    return new UuidService();
});
// Contrôleurs avec leurs dépendances
$container->set(AuthController::class, function (ServiceContainer $container) {
    $validator = $container->get(Validator::class);
    $loginValidation = new LoginValidation($validator);
    return new AuthController($loginValidation);
});

$container->set(UuidController::class, function (ServiceContainer $container) {
    $uuidService = $container->get(UuidService::class);
    return new UuidController($uuidService);
});
$container->set(ProtectedController::class, function () { // Ajouter le ProtectedController ici
    return new ProtectedController();
});
// Middlewares

$container->set(AuthMiddleware::class, function () {
    return new AuthMiddleware();
});

$container->set(LoggingMiddleware::class, function (ServiceContainer $container) {
    $logger = $container->get(Logger::class);
    return new LoggingMiddleware($logger);
});




// Retourner le conteneur configuré
return $container;