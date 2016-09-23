<?php

if (!class_exists('Composer\Autoload\ClassLoader', false)) {
    require __DIR__ . '/../vendor/autoload.php';
}

interface invokableCallback
{
    public function __invoke(\Venta\Contracts\Event\Event $event);
}