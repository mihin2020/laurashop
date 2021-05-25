<?php

class Lokalise_Loader
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var Lokalise_Registrable[]
     */
    private $components;

    /**
     * @param wpdb $wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Load plugin dependencies and register Wordpress hooks
     */
    public function load()
    {
        $authorization = new Lokalise_Authorization($this->wpdb);
        $installer = new Lokalise_Installer($this->wpdb, $this, $authorization);

        $this->register($authorization);
        $this->register($installer);
        $this->register(new Lokalise_I18n());
        $this->register(new Lokalise_Dashboard($authorization, $installer));
        $this->register(new Lokalise_Authorization_View($authorization));

        $pluginProvider = new Lokalise_PluginProvider();
        $this->register($pluginProvider);
        $this->register(new Lokalise_Rest($pluginProvider, $authorization));

        $this->register(new Lokalise_Provider_Wpml($this->wpdb));

        $this->register(new Post_Types_Provider());
        $this->register(new Lokalise_RestCallbacks());
        $this->register(new Lokalise_Callback_Get_Posts($pluginProvider));
        $this->register(new Lokalise_Callback_Update_Posts($pluginProvider));

        foreach ($this->components as $class => $component) {
            $component->register();
        }
    }

    /**
     * Register plugin component for hook registration
     *
     * @param Lokalise_Registrable $component
     */
    public function register($component)
    {
        $class = get_class($component);
        if (!($component instanceof Lokalise_Registrable)) {
            throw new InvalidArgumentException("$class is not instance of Lokalise_Registrable");
        }

        $this->components[$class] = $component;
    }

    public function getPluginData()
    {
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        return get_plugin_data(LOKALISE_FILE);
    }

    /**
     * @param $class
     *
     * @return Lokalise_Registrable
     */
    public function getComponent($class)
    {
        if (!isset($this->components[$class])) {
            throw new InvalidArgumentException("Class $class is not registered component");
        }

        return $this->components[$class];
    }

    /**
     * @return Lokalise_Authorization
     */
    public function authorization()
    {
        /** @var Lokalise_Authorization $authorization */
        $authorization = $this->getComponent(Lokalise_Authorization::class);
        return $authorization;
    }
}
