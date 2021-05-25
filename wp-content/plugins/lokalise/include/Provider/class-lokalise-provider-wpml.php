<?php

class Lokalise_Provider_Wpml extends Lokalise_Registrable implements Lokalise_Provider
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @param wpdb $wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function isEnabled()
    {
        return is_plugin_active($this->getPluginFile());
    }

    public function getSlug()
    {
        return 'wpml';
    }

    public function getName()
    {
        return 'WPML Multilingual CMS';
    }

    public function getPriority()
    {
        return 10;
    }

    public function getLocale()
    {
        return new Lokalise_Locales_Wpml($this->wpdb);
    }

    public function getDecorator()
    {
        return new Lokalise_Decorator_Wpml($this->wpdb);
    }

    public function getPluginFile()
    {
        return 'sitepress-multilingual-cms/sitepress.php';
    }

    public function register()
    {
        Lokalise_PluginProvider::registerProvider($this->getSlug(), $this);
    }
}
