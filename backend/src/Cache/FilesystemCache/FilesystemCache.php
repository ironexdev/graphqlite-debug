<?php declare(strict_types=1);

namespace App\Cache\FilesystemCache;

use Symfony\Component\Cache\Psr16Cache;

class FilesystemCache extends Psr16Cache implements FilesystemCacheInterface
{

}
