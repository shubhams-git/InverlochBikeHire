<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit1b1b13149df9b0718e5cfd9e0b7e3eb7
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

        spl_autoload_register(array('ComposerAutoloaderInit1b1b13149df9b0718e5cfd9e0b7e3eb7', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit1b1b13149df9b0718e5cfd9e0b7e3eb7', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit1b1b13149df9b0718e5cfd9e0b7e3eb7::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
