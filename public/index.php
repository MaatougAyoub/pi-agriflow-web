<?php

use App\Kernel;

if (\PHP_SAPI === 'cli-server') {
    $urlPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', \PHP_URL_PATH);
    $publicFile = __DIR__ . DIRECTORY_SEPARATOR . ltrim((string) $urlPath, '/');

    // laisser PHP servir les fichiers statiques (css/js/images)
    if (is_file($publicFile)) {
        return false;
    }
}

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};