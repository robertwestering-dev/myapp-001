<?php

use Tests\TestCase;

uses(TestCase::class);

it('uses the configured public webroot for storage links', function () {
    $previousAppPublicPath = env('APP_PUBLIC_PATH');

    putenv('APP_PUBLIC_PATH=/tmp/hermes-public');
    $_ENV['APP_PUBLIC_PATH'] = '/tmp/hermes-public';
    $_SERVER['APP_PUBLIC_PATH'] = '/tmp/hermes-public';

    $config = require base_path('config/filesystems.php');

    expect($config['links'])->toBe([
        '/tmp/hermes-public/storage' => storage_path('app/public'),
    ]);

    if ($previousAppPublicPath === false || $previousAppPublicPath === null) {
        putenv('APP_PUBLIC_PATH');
        unset($_ENV['APP_PUBLIC_PATH'], $_SERVER['APP_PUBLIC_PATH']);

        return;
    }

    putenv('APP_PUBLIC_PATH='.$previousAppPublicPath);
    $_ENV['APP_PUBLIC_PATH'] = $previousAppPublicPath;
    $_SERVER['APP_PUBLIC_PATH'] = $previousAppPublicPath;
});
