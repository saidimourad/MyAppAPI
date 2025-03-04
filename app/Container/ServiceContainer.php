<?php

namespace App\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ServiceContainer implements ContainerInterface
{
    private $services = [];

    /**
     * Enregistre un service dans le conteneur.
     *
     * @param string $id L'identifiant du service.
     * @param callable $factory Une fonction de création du service.
     */
    public function set(string $id, callable $factory): void
    {
        $this->services[$id] = $factory;
    }

    /**
     * Récupère un service depuis le conteneur.
     *
     * @param string $id L'identifiant du service.
     * @return mixed Le service demandé.
     * @throws NotFoundExceptionInterface Si le service n'est pas trouvé.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundExceptionInterface("Service '$id' not found.");
        }

        // Si le service est une factory, on l'exécute pour créer l'instance
        if (is_callable($this->services[$id])) {
            $this->services[$id] = $this->services[$id]($this);
        }

        return $this->services[$id];
    }

    /**
     * Vérifie si un service est enregistré dans le conteneur.
     *
     * @param string $id L'identifiant du service.
     * @return bool True si le service existe, sinon false.
     */
    public function has($id): bool
    {
        return isset($this->services[$id]);
    }
}