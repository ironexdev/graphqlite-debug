<?php declare(strict_types=1);

namespace App\Cache\FilesystemCache;

interface FilesystemCacheFactoryInterface
{
    public function create(string $namespace): FilesystemCacheInterface;
}
