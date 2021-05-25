<?php

class Lokalise_PluginProvider extends Lokalise_Registrable
{
    /**
     * @var Lokalise_Provider[]
     */
    private static $providers;

    /**
     * @return bool
     */
    public function hasAnyProvider()
    {
        foreach (self::$providers as $provider) {
            if ($provider->isEnabled()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Lokalise_Provider
     */
    public function getEffectiveProvider()
    {
        $providers = array_filter(self::$providers, function ($provider) {
            /** @var Lokalise_Provider $provider */
            return $provider->isEnabled();
        });

        if (empty($providers)) {
            // site provider is not registered to not mess-up provider checks
            // it is returned in special cases as default
            return new Lokalise_Provider_Site();
        }

        usort($providers, function ($a, $b) {
            /** @var Lokalise_Provider $a */
            /** @var Lokalise_Provider $b */
            return $b->getPriority() - $a->getPriority();
        });

        return reset($providers);
    }

    public function adminNotice()
    {
        if (!$this->hasAnyProvider()) {
            $providerNames = array_map(function ($provider) {
                /** @var Lokalise_Provider $provider */
                return $provider->getName();
            }, self::$providers);
            $model = new Lokalise_Model_ProviderNotice($providerNames);
            include(LOKALISE_DIR . 'template/admin-notice-providers.php');
        }
    }

    /**
     * @param string $name
     * @param Lokalise_Provider $provider
     */
    public static function registerProvider($name, $provider)
    {
        self::$providers[$name] = $provider;
    }

    public function register()
    {
        add_action('admin_notices', array($this, 'adminNotice'));
    }
}
