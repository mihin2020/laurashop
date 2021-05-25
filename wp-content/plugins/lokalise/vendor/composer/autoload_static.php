<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc8ee38302a6d69cea8bf739529668be5
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
    );

    public static $classMap = array (
        'Abstract_Lokalise_Callback' => __DIR__ . '/../..' . '/include/Callback/class-abstract-lokalise-callback.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Lokalise_Authorization' => __DIR__ . '/../..' . '/include/class-lokalise-authorization.php',
        'Lokalise_Authorization_View' => __DIR__ . '/../..' . '/include/class-lokalise-authorization-view.php',
        'Lokalise_Callback' => __DIR__ . '/../..' . '/include/lokalise-callback.php',
        'Lokalise_Callback_Get_Posts' => __DIR__ . '/../..' . '/include/Callback/class-lokalise-callback-get-posts.php',
        'Lokalise_Callback_Update_Posts' => __DIR__ . '/../..' . '/include/Callback/class-lokalise-callback-update-posts.php',
        'Lokalise_Dashboard' => __DIR__ . '/../..' . '/include/class-lokalise-dashboard.php',
        'Lokalise_Decorator' => __DIR__ . '/../..' . '/include/lokalise-decorator.php',
        'Lokalise_Decorator_Wpml' => __DIR__ . '/../..' . '/include/Decorator/class-lokalise-decorator-wpml.php',
        'Lokalise_I18n' => __DIR__ . '/../..' . '/include/class-lokalise-i18n.php',
        'Lokalise_Installer' => __DIR__ . '/../..' . '/include/class-lokalise-installer.php',
        'Lokalise_Loader' => __DIR__ . '/../..' . '/include/class-lokalise-loader.php',
        'Lokalise_Locales' => __DIR__ . '/../..' . '/include/lokalise-locales.php',
        'Lokalise_Locales_Site' => __DIR__ . '/../..' . '/include/Locales/class-lokalise-locales-site.php',
        'Lokalise_Locales_Wpml' => __DIR__ . '/../..' . '/include/Locales/class-lokalise-locales-wpml.php',
        'Lokalise_Logger' => __DIR__ . '/../..' . '/include/class-lokalise-logger.php',
        'Lokalise_Model_Authorization_Request' => __DIR__ . '/../..' . '/include/Model/class-lokalise-model-authorization-request.php',
        'Lokalise_Model_Dashboard' => __DIR__ . '/../..' . '/include/Model/class-lokalise-model-dashboard.php',
        'Lokalise_Model_Locale' => __DIR__ . '/../..' . '/include/Model/class-lokalise-model-locale.php',
        'Lokalise_Model_ProviderNotice' => __DIR__ . '/../..' . '/include/Model/class-model-lokalise-provider-notice.php',
        'Lokalise_Model_SecretNotice' => __DIR__ . '/../..' . '/include/Model/class-lokalise-model-secret-notice.php',
        'Lokalise_PluginProvider' => __DIR__ . '/../..' . '/include/class-lokalise-plugin-provider.php',
        'Lokalise_Provider' => __DIR__ . '/../..' . '/include/lokalise-provider.php',
        'Lokalise_Provider_Site' => __DIR__ . '/../..' . '/include/Provider/class-lokalise-provider-site.php',
        'Lokalise_Provider_Wpml' => __DIR__ . '/../..' . '/include/Provider/class-lokalise-provider-wpml.php',
        'Lokalise_Registrable' => __DIR__ . '/../..' . '/include/class-lokalise-registrable.php',
        'Lokalise_Rest' => __DIR__ . '/../..' . '/include/class-lokalise-rest.php',
        'Lokalise_RestCallbacks' => __DIR__ . '/../..' . '/include/class-lokalise-rest-callbacks.php',
        'Post_Types_Provider' => __DIR__ . '/../..' . '/include/Model/class-lokalise-post-types-provider.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitc8ee38302a6d69cea8bf739529668be5::$classMap;

        }, null, ClassLoader::class);
    }
}
