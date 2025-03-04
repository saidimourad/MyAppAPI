<?php
namespace App\Services;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class UuidService
{
    private $cache;

    public function __construct()
    {
        $this->cache = new FilesystemAdapter();
    }

    public function generateUuid(): string
    {
        $uuidItem = $this->cache->getItem('uuid');

        if (!$uuidItem->isHit()) {
            // Si le UUID n'est pas trouvé dans le cache, on en génère un nouveau
            $uuid = Uuid::uuid4()->toString();
            $uuidItem->set($uuid);
            $uuidItem->expiresAfter(60);  // Expiration après 60 secondes
            $this->cache->save($uuidItem);
        } else {
            // Sinon, on récupère le UUID existant
            $uuid = $uuidItem->get();
        }

        return $uuid;
    }
}