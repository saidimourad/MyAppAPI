<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configurer Whoops pour la gestion des erreurs en fonction de l'environnement
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

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