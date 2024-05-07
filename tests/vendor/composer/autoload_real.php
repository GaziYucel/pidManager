<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitd129e47fcb8ccfa4857a7e42930b1cc8
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitd129e47fcb8ccfa4857a7e42930b1cc8', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitd129e47fcb8ccfa4857a7e42930b1cc8', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitd129e47fcb8ccfa4857a7e42930b1cc8::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}