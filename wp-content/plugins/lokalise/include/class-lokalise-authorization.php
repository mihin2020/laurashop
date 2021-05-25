<?php

class Lokalise_Authorization extends Lokalise_Registrable
{
    const SECRET_OPTION = 'lokalise_secret';
    const SECRET_LENGTH = 64;

    const AUTH_HEADER_PATTERN = '~^Bearer (?<token>(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?)$~';

    public static $isAuthorized = false;

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

    /**
     * @return string|null
     */
    public function getSecret()
    {
        if (!defined('LOKALISE_SECRET')) {
            $secret = get_option(self::SECRET_OPTION);
        } else {
            $secret = LOKALISE_SECRET;
        }

        if ($secret === false || empty($secret) || strlen($secret) !== self::SECRET_LENGTH) {
            $secret = null;
        }

        return $secret;
    }

    /**
     * @param string $redirectUri
     * @return bool
     */
    public function isRedirectUriValid($redirectUri)
    {
        return 1 === preg_match(
            '~^https://' . preg_quote(LOKALISE_APP) . '/wordpress-redirect(\?.*|$)~',
            $redirectUri
        );
    }

    public function createGuardCookie()
    {
        $this->setCookie(time());
    }

    public function removeGuardCookie()
    {
        $this->setCookie(null, true);
    }

    private function setCookie($value, $remove = false)
    {
        $parsedUrl = parse_url(site_url());
        $path = '/';
        if (isset($parsedUrl['path'])) {
            $path = $parsedUrl['path'];
        }

        $expire = LOKALISE_AUTH_TIMEOUT;
        if ($remove) {
            // use negative offset to remove cookie
            $expire *= -1;
        }

        $secure = 'https' === $parsedUrl['scheme'];

        setcookie(
            LOKALISE_AUTH_GUARD_COOKIE_NAME,
            $value,
            time() + $expire,
            $path,
            $parsedUrl['host'],
            $secure,
            true
        );
    }

    /**
     * @return bool
     */
    public function isGuardCookieValid()
    {
        return isset($_COOKIE[LOKALISE_AUTH_GUARD_COOKIE_NAME])
            && ((int)$_COOKIE[LOKALISE_AUTH_GUARD_COOKIE_NAME]) + LOKALISE_AUTH_TIMEOUT > time();

    }

    /**
     * @param string $redirectUri
     * @return Lokalise_Model_Authorization_Request
     */
    public function buildRequestModel($redirectUri)
    {
        return new Lokalise_Model_Authorization_Request($redirectUri);
    }

    public function createRequestCode()
    {
        $userData = wp_get_current_user();

        $requestCode = hash('sha512', uniqid('lokalise_' . $userData->ID, true));

        $this->wpdb->insert($this->wpdb->prefix . 'lokalise_auth_tokens', array(
            'user_id' => $userData->ID,
            'request_code' => $requestCode,
            'valid_before' => date('Y-m-d H:i:s', time() + LOKALISE_AUTH_TIMEOUT),
        ));

        return $requestCode;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function createToken($request)
    {
        $code = $request->get_param('code');
        $secret = $request->get_param('secret');
        $redirectUri = $request->get_param('redirect_uri');

        if (empty($code) || empty($secret) || empty($redirectUri)) {
            return new WP_Error('missing_params', "Missing request parameters", array('status' => 400));
        }

        if (!$this->isRedirectUriValid($redirectUri)) {
            return new WP_Error('invalid_redirect_uri', "Invalid redirect URI", array('status' => 400));
        }

        if ($this->getSecret() === null || $secret !== $this->getSecret()) {
            return new WP_Error('invalid_secret', "Invalid Lokalise secret", array('status' => 400));
        }

        $authToken = $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT id, user_id
            FROM {$this->wpdb->prefix}lokalise_auth_tokens
            WHERE request_code = %s AND valid_before IS NOT NULL AND valid_before > %s",
            $code,
            date('Y-m-d H:i:s')
        ), ARRAY_A);

        if ($authToken === null) {
            return new WP_Error('invalid_code', "Invalid request code", array('status' => 400));
        }

        // take only 94 characters starting from generated hash
        // this will ensure that resulting base64 string is exactly 64 characters long
        $tokenRaw = substr(hash('sha512', uniqid('lokalise_' . $authToken['user_id'], true)), 0, 94);
        $tokenBin = hex2bin($tokenRaw);
        $token = base64_encode($tokenBin);

        // generate weak hash just for verification
        // this will be used upon request to check if token is issued with current LOKALISE_SECRET
        $verify = hash('md5', $tokenBin . $this->getSecret());

        $this->wpdb->update($this->wpdb->prefix . 'lokalise_auth_tokens', array(
            'token' => $token,
            'request_code' => null, // remove request code
            'valid_before' => null, // valid_before is used for request_code only, token has indefinite lifespan
            'verify' => $verify, // verify is meant to invalidate all issued tokens
        ), array(
            'id' => $authToken['id'],
        ), array('%s', '%s', '%s'), array('%d'));

        $data = array(
            'token' => $token,
        );
        return rest_ensure_response($data);
    }

    public function authorizationRestInit()
    {
        register_rest_route(LOKALISE_REST_NS, 'auth/tokens', array(
            'methods' => 'POST',
            'callback' => array($this, 'createToken'),
            'args' => array(
                'code' => array(
                    'type'        => 'string',
                    'description' => __("Authorization token request code", 'lokalise'),
                    'required'    => true,
                ),
                'secret' => array(
                    'type'        => 'string',
                    'description' => __("Authorization secret found at Settings > Lokalise", 'lokalise'),
                    'required'    => true,
                ),
                'redirect_uri' => array(
                    'type'        => 'string',
                    'description' => __("Redirect URI for remote service validation", 'lokalise'),
                    'required'    => true,
                ),
            ),
        ));
    }

    public function adminNotice()
    {
        if ($this->getSecret() === null) {
            $model = new Lokalise_Model_SecretNotice([
                $this->generateSecret(self::SECRET_LENGTH),
                $this->generateSecret(self::SECRET_LENGTH),
                $this->generateSecret(self::SECRET_LENGTH),
            ]);
            include(LOKALISE_DIR . 'template/admin-notice-secret.php');
        }
    }

    public function register()
    {
        add_action('rest_api_init', array($this, 'authorizationRestInit'));
        add_filter('determine_current_user', array($this, 'authorizeRequest'));
        add_action('admin_notices', array($this, 'adminNotice'));
    }

    public function getRequestHeaders()
    {
        $baseHeaders = [];
        if (function_exists('apache_request_headers')) {
            $baseHeaders = apache_request_headers();
        }

        return array_merge($baseHeaders, getallheaders());
    }

    public function authorizeRequest($userId)
    {
        if ($userId && $userId > 0) {
            return (int)$userId;
        }

        if ($this->getSecret() === null) {
            return null;
        }

        $requestHeaders = $this->getRequestHeaders();
        if (empty($requestHeaders['Authorization'])) {
            return null;
        }

        $match = array();
        // match base64 value in header
        if (!preg_match(self::AUTH_HEADER_PATTERN, $requestHeaders['Authorization'], $match)) {
            return null;
        }

        $tokenRaw = base64_decode($match['token']);
        if ($tokenRaw === false) {
            return null;
        }

        $verify = hash('md5', $tokenRaw . $this->getSecret());
        $userId = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT user_id
            FROM {$this->wpdb->prefix}lokalise_auth_tokens
            WHERE token = %s AND verify = %s",
            $match['token'],
            $verify
        ));

        if ($userId === null) {
            return null;
        }

        self::$isAuthorized = true;

        return (int)$userId;
    }

    public function generateSecret($length)
    {
        $secret = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $secret .= $codeAlphabet[$this->cryptoRandSecure(0, $max-1)];
        }

        return $secret;
    }

    private function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }
}
