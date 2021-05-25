<?php

class Lokalise_Authorization_View extends Lokalise_Registrable
{
    /**
     * @var Lokalise_Authorization
     */
    private $authorization;
    /**
     * @var bool Whenever Lokalise authorization form is requested
     */
    private $formFlag = false;
    /**
     * @var string URI to return user after authorization
     */
    private $redirectUri;
    /**
     * @var string[] List of errors
     */
    private $errors = [];

    /**
     * @param Lokalise_Authorization $authorization
     */
    public function __construct($authorization)
    {
        $this->authorization = $authorization;
    }

    public function authorizationForm()
    {
        $this->formFlag = true;

        // URI that we are requesting - user is redirected to it after sing-in
        $requestUri = $_SERVER['REQUEST_URI'];

        // Check if user is signed in
        if (!is_user_logged_in()) {
            wp_redirect(wp_login_url($requestUri));
            exit;
        }

        if ($this->authorization->getSecret() === null) {
            $this->errors['lokalise_secret_missing'] = sprintf(__(
                "Authorization secret is not set-up.<br />" .
                "Generate secret in <i>Settings &gt; Lokalise.</i><br />" .
                "Or add <code>%s</code> to your <code>wp-config.php</code>"
            ), "define('LOKALISE_SECRET', '&lt;secret&gt;');", 'lokalise');
        }

        if (!current_user_can('edit_posts')) {
            $this->errors['missing_permission_posts'] = __(
                "Current user does not have permission to edit posts!<br />" .
                "Switch user or change user role."
            );
        }

        if (!current_user_can('edit_pages')) {
            $this->errors['missing_permission_pages'] = __(
                "Current user does not have permission to edit pages!<br />" .
                "Switch user or change user role."
            );
        }

        $this->redirectUri = $this->acquireRedirectUri();

        $this->handleUserAction();
    }

    private function handleUserAction()
    {
        if (!isset($_GET['choice'])) {
            $this->authorization->createGuardCookie();
            return;
        }

        $model = new Lokalise_Model_Authorization_Request($this->redirectUri);
        switch ($_GET['choice']) {
            case Lokalise_Model_Authorization_Request::AUTH_ACCEPT:
                if (!$this->authorization->isGuardCookieValid()) {
                    $this->errors['auth_guard_cookie'] = __("Invalid guard cookie!", 'lokalise');
                    return;
                }

                // remove guard cookie
                $this->authorization->removeGuardCookie();

                // generate token request code
                $requestCode = $this->authorization->createRequestCode();

                wp_redirect($model->getAcceptUrl($requestCode));
                die;
                break;
            case Lokalise_Model_Authorization_Request::AUTH_REJECT:
                // remove guard cookie
                $this->authorization->removeGuardCookie();

                wp_redirect($model->getRejectUrl());
                die;
                break;
            case Lokalise_Model_Authorization_Request::AUTH_RETURN:
                // remove guard cookie
                $this->authorization->removeGuardCookie();

                wp_redirect($model->getReturnUrl());
                die;
                break;
            default:
                // this is invalid choice - do nothing
                break;
        }
    }

    private function acquireRedirectUri()
    {
        if (!isset($_GET['redirect_uri'])) {
            $this->errors['redirect_uri_not_provided'] = __("Redirect URI is not provided.", 'lokalise');
            return null;
        }

        if (!$this->authorization->isRedirectUriValid($_GET['redirect_uri'])) {
            $this->errors['redirect_uri_not_valid'] = __("Redirect URI is not valid.", 'lokalise');
            return null;
        }

        return $_GET['redirect_uri'];
    }

    public function loginTitle($login_title, $title)
    {
        if (!$this->formFlag) {
            return $login_title;
        }

        $info = get_bloginfo('name', 'display');
        return __("Lokalise authorization - ", 'lokalise') . $info;
    }

    public function loginForm()
    {
        if (!$this->formFlag) {
            return;
        }

        $model = new Lokalise_Model_Authorization_Request($this->redirectUri);
        $model->setValid(empty($this->errors));
        include(LOKALISE_DIR . 'template/authorization.php');
    }

    public function loginScripts()
    {
        if (!$this->formFlag) {
            return;
        }

        // load scripts and styles to customize login page
        wp_register_script('lokalise-authorization-page', LOKALISE_URL . 'public/authorization.js');
        wp_register_style('lokalise-authorization-page', LOKALISE_URL . 'public/authorization.css');
        wp_enqueue_script('lokalise-authorization-page');
        wp_enqueue_style('lokalise-authorization-page');
    }

    /**
     * @param WP_Error $errors
     * @param string|null $redirect_to
     * @return WP_Error
     */
    public function loginErrors($errors, $redirect_to)
    {
        if (!$this->formFlag) {
            return $errors;
        }

        foreach ($this->errors as $code => $error) {
            $errors->add($code, $error);
        }

        return $errors;
    }

    public function register()
    {
        add_filter('login_form_lokalise_auth', array($this, 'authorizationForm'));
        add_filter('login_title', array($this, 'loginTitle'), 10, 2);
        add_action('login_form', array($this, 'loginForm'));
        add_action('login_enqueue_scripts', array($this, 'loginScripts'));
        add_filter( 'wp_login_errors', array($this, 'loginErrors'), 10, 2);
    }
}
