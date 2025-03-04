 
# Projet API

## Description

Ce projet est une mini-application API en PHP qui utilise plusieurs packages pour gérer le routing, la validation, l'authentification JWT, la génération d'UUID, et la gestion des erreurs. L'application est containerisée avec Docker.

## Installation

1. Clonez le dépôt `https://github.com/saidimourad/MyAppAPI.git`.
2. Exécutez `docker-compose up --build` pour construire et lancer l'application.
3. L'application sera disponible sur `http://localhost:8080`.

## Endpoints

- `GET /api/uuid` : Génère et retourne un UUID.
- `POST /api/login` : Authentifie un utilisateur et retourne un JWT **username : admin et password :secret**.
- `GET /api/protected` : Endpoint protégé qui requiert un token JWT valide.

## Configuration

Les variables d'environnement sont chargées depuis le fichier `.env`. Assurez-vous de configurer `JWT_SECRET` pour l'authentification JWT.


## Documentation Technique

### 1. **Routing et Middleware**

- **`nikic/fast-route`** : 
  - **Raison du choix** : Utilisé pour gérer le **routing** de l'application. `nikic/fast-route` est une bibliothèque légère et rapide pour gérer les routes, idéale pour les petites applications comme celle-ci. Elle permet de définir des routes et d'y associer des contrôleurs ou des fonctions de manière efficace.
  
- **`psr/http-server-middleware`** : 
  - **Raison du choix** : Ce package définit des interfaces pour les **middlewares**. Les middlewares sont des composants intermédiaires qui peuvent être exécutés avant ou après qu'une requête HTTP soit traitée par l'application. Par exemple, on peut y inclure des middlewares pour la gestion de l'authentification, des erreurs, ou des en-têtes CORS.

- **`psr/http-server-handler`** : 
  - **Raison du choix** : Fournit une interface pour le gestionnaire de requêtes HTTP. Cela permet de structurer le traitement des requêtes et d'exécuter des middlewares de manière standardisée en suivant la spécification PSR-7.

### 2. **Validation et Gestion d'Erreurs**

- **`illuminate/validation`** : 
  - **Raison du choix** : Ce package est utilisé pour la **validation des entrées utilisateurs**. Il permet de valider les données reçues dans les requêtes (par exemple, vérifier que les champs sont correctement formatés). Il provient de l'écosystème Laravel, offrant des fonctionnalités de validation puissantes et extensibles.
  
- **`filp/whoops`** : 
  - **Raison du choix** : `filp/whoops` est utilisé pour afficher des **erreurs détaillées et conviviales** en environnement de développement. Cela permet de mieux comprendre les erreurs pendant le développement, avec des informations sur les exceptions, les traces de pile et les fichiers où l'erreur s'est produite. Il est désactivé en production pour des raisons de sécurité.

### 3. **Authentification**

- **`firebase/php-jwt`** : 
  - **Raison du choix** : Ce package permet de gérer l'authentification basée sur des **tokens JWT**. Les JWT sont utilisés pour authentifier les utilisateurs sans avoir besoin de sessions sur le serveur. Ce mécanisme est très utile pour les applications stateless, car les informations sur l'utilisateur sont contenues dans le token lui-même, signé avec une clé secrète.

### 4. **Génération d'Identifiants**

- **`ramsey/uuid`** : 
  - **Raison du choix** : Ce package est utilisé pour générer des **UUIDs** (identifiants uniques universels). Les UUID sont très utiles pour garantir l'unicité des ressources dans un système distribué, surtout dans les APIs où les ressources peuvent être créées de manière indépendante sur plusieurs serveurs.

### 5. **Gestion d'Environnement**

- **`vlucas/phpdotenv`** : 
  - **Raison du choix** : Ce package permet de charger facilement des variables d'environnement à partir d'un fichier `.env`. Il est utilisé pour stocker des informations sensibles ou des paramètres de configuration, comme des clés API ou des secrets JWT, et il aide à séparer la configuration du code source, ce qui est une bonne pratique en matière de sécurité et de gestion d'environnement.

### 6. **Divers**

- **`psr/log`** : 
  - **Raison du choix** : Ce package définit des interfaces pour un système de **journalisation (logging)** dans l'application. Il permet de suivre les erreurs, les requêtes et d'autres événements importants qui se produisent dans l'application. Utiliser une interface standardisée garantit la flexibilité, et permet d'intégrer facilement différents systèmes de journalisation (comme `monolog`).

- **`psr/container`** : 
  - **Raison du choix** : Ce package définit l'interface pour un **container d'injection de dépendances** (DI). L'injection de dépendances permet de séparer les responsabilités dans l'application et facilite la gestion des dépendances entre les différentes classes, comme les services ou les contrôleurs.

- ### 7 .**Containerisation** : Docker avec un Dockerfile et un fichier `docker-compose.yml`.

---

Chaque bibliothèque a été choisie pour sa simplicité, sa performance ou sa conformité aux standards de l'industrie, afin de garantir une application légère, rapide et extensible.

## Structure du Projet

/MyAppAPI
│
├── /app
│   ├── /Controllers
│   │   ├── AuthController.php
│   │   ├── UuidController.php
│   │   └── ProtectedController.php
│   ├── /Middleware
│   │   ├── AuthMiddleware.php
│   │   └── LoggingMiddleware.php
│   ├── /Services
│   │   └── UuidService.php
│   ├── /Validation
│   │   └── LoginValidation.php
│   └── bootstrap.php
│
├── /config
│   └── routes.php
│
├── /public
│   └── index.php
│
├── /vendor
│
├── /var
│   └── /cache
│
├── .env
├── .env.example
├── Dockerfile
├── docker-compose.yml
├── composer.json
├── composer.lock
└── README.md

### Description des Dossiers

- **`/app`** : Contient les fichiers logiques de l'application, organisés en sous-dossiers tels que `Controllers`, `Middleware`, `Services`, et `Validation`.
  - **`Controllers`** : Les contrôleurs contiennent la logique des actions à réaliser lorsqu'un endpoint est appelé. Par exemple, `AuthController.php` gère l'authentification des utilisateurs.
  - **`Middleware`** : Les middlewares traitent les requêtes avant qu'elles n'atteignent les contrôleurs. Par exemple, `AuthMiddleware.php` vérifie si le token JWT est valide avant d'autoriser l'accès à un endpoint protégé.
  - **`Services`** : Contient la logique métier, comme la génération d'UUID dans `UuidService.php`.
  - **`Validation`** : Contient les règles de validation des données envoyées par les utilisateurs, comme `LoginValidation.php` pour la validation des informations de connexion.

- **`/config`** : Contient les fichiers de configuration, tels que `routes.php`, où les routes de l'application sont définies.

- **`/public`** : Contient le fichier `index.php`, qui est le point d'entrée de l'application. C'est ici que le serveur Web pointe pour démarrer l'application.

- **`/vendor`** : Contient les dépendances installées via Composer.

