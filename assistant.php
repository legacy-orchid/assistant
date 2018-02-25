#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->overload();

$app = new App\App();

$app->run([
    App\Actions\HelloAction::class,
    App\Actions\MissingDataAction::class,
    App\Actions\DocsAction::class,
    App\Actions\ByeAction::class,
]);
