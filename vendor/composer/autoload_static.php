<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit15d5b6ff68f0a1ed6524c4b013f4e435
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'APP\\plugins\\generic\\pidManager\\classes\\' => 39,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'APP\\plugins\\generic\\pidManager\\classes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static $classMap = array (
        'APP\\plugins\\generic\\pidManager\\classes\\Db\\PluginSchema' => __DIR__ . '/../..' . '/classes/Db/PluginSchema.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit15d5b6ff68f0a1ed6524c4b013f4e435::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit15d5b6ff68f0a1ed6524c4b013f4e435::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit15d5b6ff68f0a1ed6524c4b013f4e435::$classMap;

        }, null, ClassLoader::class);
    }
}
