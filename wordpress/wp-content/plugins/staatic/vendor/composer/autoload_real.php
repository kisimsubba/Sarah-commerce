<?php

namespace Staatic\Vendor;

use Staatic\Vendor\Composer\Autoload\ClassLoader;
use Staatic\Vendor\Composer\Autoload\ComposerStaticInitf232db2868c29ffaa5130631a4453cff;
// autoload_real.php @generated by Composer
class ComposerAutoloaderInitf232db2868c29ffaa5130631a4453cff
{
    private static $loader;
    public static function loadClassLoader($class)
    {
        if ('Staatic\\Vendor\\Composer\\Autoload\\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }
    /**
     * @return ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }
        require __DIR__ . '/platform_check.php';
        \spl_autoload_register(array('Staatic\\Vendor\\ComposerAutoloaderInitf232db2868c29ffaa5130631a4453cff', 'loadClassLoader'), \true, \true);
        self::$loader = $loader = new ClassLoader(\dirname(__DIR__));
        \spl_autoload_unregister(array('Staatic\\Vendor\\ComposerAutoloaderInitf232db2868c29ffaa5130631a4453cff', 'loadClassLoader'));
        require __DIR__ . '/autoload_static.php';
        \call_user_func(ComposerStaticInitf232db2868c29ffaa5130631a4453cff::getInitializer($loader));
        $loader->setClassMapAuthoritative(\true);
        $loader->register(\true);
        $includeFiles = ComposerStaticInitf232db2868c29ffaa5130631a4453cff::$files;
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequiref232db2868c29ffaa5130631a4453cff($fileIdentifier, $file);
        }
        return $loader;
    }
}
/**
 * @param string $fileIdentifier
 * @param string $file
 * @return void
 */
function composerRequiref232db2868c29ffaa5130631a4453cff($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = \true;
        require $file;
    }
}
