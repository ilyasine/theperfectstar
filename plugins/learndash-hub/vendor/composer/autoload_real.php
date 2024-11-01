<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit64f45529de2a1e9c898d5a4f004fc727
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

        spl_autoload_register(array('ComposerAutoloaderInit64f45529de2a1e9c898d5a4f004fc727', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit64f45529de2a1e9c898d5a4f004fc727', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit64f45529de2a1e9c898d5a4f004fc727::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
