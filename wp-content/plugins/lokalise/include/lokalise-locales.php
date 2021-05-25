<?php

interface Lokalise_Locales
{
    /**
     * @return Lokalise_Model_Locale[]
     * @throws Exception
     */
    public function getLocales();
}
