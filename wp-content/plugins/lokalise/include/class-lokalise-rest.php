<?php

class Lokalise_Rest extends Lokalise_Registrable
{
    /**
     * @var Lokalise_PluginProvider
     */
    private $pluginProvider;
    /**
     * @var Lokalise_Authorization
     */
    private $authorization;

    /**
     * @param Lokalise_PluginProvider $pluginProvider
     * @param Lokalise_Authorization $authorization
     */
    public function __construct($pluginProvider, $authorization)
    {
        $this->pluginProvider = $pluginProvider;
        $this->authorization = $authorization;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function getLocales($request)
    {
        try {
            $provider = $this->pluginProvider->getEffectiveProvider();
            $localesProvider = $provider->getLocale();
            $locales = $localesProvider->getLocales();
        } catch (Exception $exception) {
            return new WP_REST_Response(array(
                'message' => $exception->getMessage(),
            ), 500);
        }

        return rest_ensure_response($locales);
    }

    private function getAuthorizationHeaderMessage()
    {
        $requestHeaders = $this->authorization->getRequestHeaders();
        if (!isset($requestHeaders['Authorization'])) {
            return 'missing';
        }

        if (empty($requestHeaders['Authorization'])) {
            return 'empty';
        }

        $match = [];
        if (!preg_match(Lokalise_Authorization::AUTH_HEADER_PATTERN, $requestHeaders['Authorization'], $match)) {
            return 'invalid_format';
        }

        if (base64_decode($match['token']) === false) {
            return 'invalid_token';
        }

        if (!Lokalise_Authorization::$isAuthorized) {
            // user is not authorized by Lokalise authorization mechanism
            // but header and token is fine
            return 'pass';
        }

        return 'ok';
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function getEnvironment($request)
    {
        try {
            $provider = $this->pluginProvider->getEffectiveProvider();
            $localesProvider = $provider->getLocale();
            $locales = $localesProvider->getLocales();
            $authorizationMessage = $this->getAuthorizationHeaderMessage();
            $supportedPostTypes = Post_Types_Provider::getSupportedPostTypes();

            $environment = [
                'provider' => $provider->getSlug(),
                'default_locale' => reset($locales)->code,
                'authorization' => $authorizationMessage,
                'supportedPostTypes' => $supportedPostTypes,
            ];
        } catch (Exception $exception) {
            return new WP_REST_Response([
                'message' => $exception->getMessage(),
            ], 500);
        }

        return rest_ensure_response($environment);
    }

    public function restInit()
    {
        register_rest_route('lokalise/v1', 'locales', array(
            'methods' => 'GET',
            'callback' => array($this, 'getLocales'),
            'args' => array(),
        ));
        register_rest_route('lokalise/v1', 'environment', array(
            'methods' => 'GET',
            'callback' => array($this, 'getEnvironment'),
            'args' => array(),
        ));
    }

    public function register()
    {
        add_action('rest_api_init', [$this, 'restInit']);
    }
}
