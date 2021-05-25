<?php

class Lokalise_Locales_Wpml implements Lokalise_Locales
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @param wpdb $wpdb
     */
    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * @return Lokalise_Model_Locale[]
     * @throws Exception
     */
    public function getLocales()
    {
        $wpmlLocales = $this->wpdb->get_results(
            "SELECT
                  english_name,
                  code
                FROM {$this->wpdb->prefix}icl_languages
                WHERE active = 1"
        );

        return array_map(function ($locale) {
            return new Lokalise_Model_Locale($locale->english_name, $locale->code);
        }, $wpmlLocales);
    }
}
