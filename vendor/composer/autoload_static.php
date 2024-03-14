<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit12c2585846a027112cbc326ce5955032
{
    public static $prefixLengthsPsr4 = array (
        'f' => 
        array (
            'fwai\\' => 5,
        ),
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'fwai\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit12c2585846a027112cbc326ce5955032::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit12c2585846a027112cbc326ce5955032::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit12c2585846a027112cbc326ce5955032::$classMap;

        }, null, ClassLoader::class);
    }
}
