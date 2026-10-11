<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$basePath = dirname(__DIR__);

$kernel = require $basePath . '/app/bootstrap.php';
$kernel->handle();
