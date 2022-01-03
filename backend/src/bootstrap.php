<?php declare(strict_types=1);

use App\Kernel;
use DI\ContainerBuilder;

const APP_DIRECTORY = __DIR__;

require_once APP_DIRECTORY . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

date_default_timezone_set("UTC");

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config-di.php");

$container = $containerBuilder->build();

$container->make(Kernel::class);
