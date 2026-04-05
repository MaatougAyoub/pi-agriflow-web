<?php

use App\Kernel;

if (\PHP_SAPI === 'cli-server') {
    $urlPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', \PHP_URL_PATH);
    $publicFile = __DIR__.DIRECTORY_SEPARATOR.ltrim((string) $urlPath, '/');

    // houni n5alliw php built-in server yservi les fichiers statiques direct bech css/js/images ma ydouzouch l Symfony
    if (is_file($publicFile)) {
        return false;
    }
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
