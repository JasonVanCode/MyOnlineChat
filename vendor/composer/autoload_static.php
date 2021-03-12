<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2449c07d75813c698cfb77b2902d2412
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
        'G' => 
        array (
            'GlobalData\\' => 11,
        ),
        'C' => 
        array (
            'Channel\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman',
        ),
        'GlobalData\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/globaldata/src',
        ),
        'Channel\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/channel/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2449c07d75813c698cfb77b2902d2412::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2449c07d75813c698cfb77b2902d2412::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2449c07d75813c698cfb77b2902d2412::$classMap;

        }, null, ClassLoader::class);
    }
}