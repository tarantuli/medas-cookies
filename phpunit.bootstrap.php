<?php

declare(strict_types=1);

use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\Cookies\CookiesPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(ObjectInstantiator::class);

    $config->addPackages([
        CookiesPackage::instance(),
    ]);

    return $config;
});
