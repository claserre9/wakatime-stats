<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Claserre9\WakatimeStats\Config;
use Claserre9\WakatimeStats\StatsUpdateService;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

try {
    $config = Config::fromEnv();
    // For backward compatibility with classes reading INPUT_* vars
    $config->exportInputsToServer();

    $service = new StatsUpdateService($config);
    $service->run();
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
