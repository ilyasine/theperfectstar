<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcef8a62a26e2e43e6d3efc5a3d6e4d83
{
    public static $files = array (
        '9680a2abca0f3f510cf2fd1b6d61afe6' => __DIR__ . '/../..' . '/boot/globals.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPFluent\\' => 9,
        ),
        'F' => 
        array (
            'FluentSupport\\Framework\\' => 24,
            'FluentSupport\\App\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPFluent\\' => 
        array (
            0 => __DIR__ . '/..' . '/wpfluent/framework/src/WPFluent',
        ),
        'FluentSupport\\Framework\\' => 
        array (
            0 => __DIR__ . '/..' . '/wpfluent/framework/src/WPFluent',
        ),
        'FluentSupport\\App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FluentSupport\\Database\\DBMigrator' => __DIR__ . '/../..' . '/database/DBMigrator.php',
        'FluentSupport\\Database\\DBSeeder' => __DIR__ . '/../..' . '/database/DBSeeder.php',
        'FluentSupport\\Database\\Migrations\\AIActivityLogsMigrator' => __DIR__ . '/../..' . '/database/Migrations/AIActivityLogsMigrator.php',
        'FluentSupport\\Database\\Migrations\\ActivityMigrator' => __DIR__ . '/../..' . '/database/Migrations/ActivityMigrator.php',
        'FluentSupport\\Database\\Migrations\\AttachmentsMigrator' => __DIR__ . '/../..' . '/database/Migrations/AttachmentsMigrator.php',
        'FluentSupport\\Database\\Migrations\\ConversationsMigrator' => __DIR__ . '/../..' . '/database/Migrations/ConversationsMigrator.php',
        'FluentSupport\\Database\\Migrations\\DataMetrixMigrator' => __DIR__ . '/../..' . '/database/Migrations/DataMetrixMigrator.php',
        'FluentSupport\\Database\\Migrations\\MailBoxMigrator' => __DIR__ . '/../..' . '/database/Migrations/MailBoxMigrator.php',
        'FluentSupport\\Database\\Migrations\\MetaMigrator' => __DIR__ . '/../..' . '/database/Migrations/MetaMigrator.php',
        'FluentSupport\\Database\\Migrations\\PersonsMigrator' => __DIR__ . '/../..' . '/database/Migrations/PersonsMigrator.php',
        'FluentSupport\\Database\\Migrations\\ProductsMigrator' => __DIR__ . '/../..' . '/database/Migrations/ProductsMigrator.php',
        'FluentSupport\\Database\\Migrations\\TagRelationsMigrator' => __DIR__ . '/../..' . '/database/Migrations/TagRelationsMigrator.php',
        'FluentSupport\\Database\\Migrations\\TaggablesMigrator' => __DIR__ . '/../..' . '/database/Migrations/TaggablesMigrator.php',
        'FluentSupport\\Database\\Migrations\\TicketsMigrator' => __DIR__ . '/../..' . '/database/Migrations/TicketsMigrator.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcef8a62a26e2e43e6d3efc5a3d6e4d83::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcef8a62a26e2e43e6d3efc5a3d6e4d83::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcef8a62a26e2e43e6d3efc5a3d6e4d83::$classMap;

        }, null, ClassLoader::class);
    }
}