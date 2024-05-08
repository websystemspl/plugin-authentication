<?php

namespace websystemspl;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class PluginAuthentication
{
    private const PLUGIN_KEY = '-license-plugin_key';
    private const API_DOMAIN = 'https://admin.k4.pl';
    private const ACTIVATE_POINT = '/api/plugin/activate';
    private const DEACTIVATE_POINT = '/api/plugin/deactivate';
    private const CHECKER_POINT = '/api/plugin/update/get_metadata/';

    /** @var String */
    private $adminMenuPageParent;

    /** @var String */
    private $pluginName;
    
    /** @var String */
    private $pluginSlug;

    public function __construct(string $pluginName, string $pluginSlug, ?string $adminMenuPageParent = null)
    {
        $this->pluginName = $pluginName;
        $this->pluginSlug = $pluginSlug;
        $this->adminMenuPageParent = $adminMenuPageParent;
    }

    /**
     * Start app
     *
     * @return void
     */
    public function boot(): void
    {
        add_action('admin_menu', [$this, 'createAdminMenuPage'], 999);
        add_action('admin_enqueue_scripts', [$this, 'adminAssets'], 99);
        $this->updateListener();
    }

    /**
     * Set admin menu page parent
     *
     * @param string $adminMenuPageParent
     * @return void
     */
    public function setAdminMenuPageParent(string $adminMenuPageParent): void
    {
        $this->adminMenuPageParent = $adminMenuPageParent;
    }

    /**
     * Create admin menu page
     *
     * @return void
     */
    public function createAdminMenuPage(): void
    {
        if(null !== $this->adminMenuPageParent) {
            add_submenu_page( 
                $this->adminMenuPageParent,
                apply_filters('wpa-page-title-' . $this->pluginSlug, __( 'License', 'ws-plugin-authentication' ) . ' ' . $this->pluginName), 
                apply_filters('wpa-menu-title-' . $this->pluginSlug, __( 'License', 'ws-plugin-authentication' ) . ' ' . $this->pluginName),
                'manage_options',
                $this->pluginSlug . '-ws-plugin-authentication-page',
                [$this, 'adminMenuPageHandler'],
                10
            );             
        } else {
            add_menu_page(
                apply_filters('wpa-menu-title-' . $this->pluginSlug, __( 'License', 'ws-plugin-authentication' ) . ' ' . $this->pluginName),
                apply_filters('wpa-menu-title-' . $this->pluginSlug, __( 'License', 'ws-plugin-authentication' ) . ' ' . $this->pluginName),
                'manage_options',
                $this->pluginSlug . '-ws-plugin-authentication-page',
                [$this, 'adminMenuPageHandler'],
                'dashicons-admin-network',
                10
            );
        }
    }

    /**
     * Listen for updates
     *
     * @return void
     */
    private function updateListener(): void
    {
        $keyDomain = unserialize(get_option($this->pluginSlug . self::PLUGIN_KEY));
        if(false !== $keyDomain) {
            PucFactory::buildUpdateChecker(
                self::API_DOMAIN . self::CHECKER_POINT . $this->pluginSlug . '/' . $keyDomain['key'] . '/' . $keyDomain['domain'],
                __FILE__,
                $this->pluginSlug,
            );
        }
    }

    /**
     * Admin menu page controller
     *
     * @return void
     */
    public function adminMenuPageHandler(): void
    {
        $errors = '';
        $keyDomain = unserialize(get_option($this->pluginSlug . self::PLUGIN_KEY));

        // ACTIVATE ACTION
        if(isset($_POST['activate'])) {     
            if(false === $keyDomain) {
                $response = wp_remote_post(self::API_DOMAIN . self::ACTIVATE_POINT, [
                    'body' => [
                        'key'         => $_POST['key'],
                        'domain'      => $_SERVER['HTTP_HOST'],
                        'plugin_slug' => $this->pluginSlug,
                    ]
                ]);
        
                $responseDecoded = json_decode($response['body'], true);

                if(isset($responseDecoded['plugin']) && $responseDecoded['plugin'] == 'activated') {
                    update_option($this->pluginSlug . self::PLUGIN_KEY, serialize([
                        'key' => $_POST['key'],
                        'domain' => $_SERVER['HTTP_HOST']
                    ]));
                    header("Refresh:1");
                } elseif(isset($responseDecoded['error'])) {
                    $errors = $responseDecoded['error'];
                } else {
                    $errors = __( 'Something went wrong.', 'ws-plugin-authentication' );
                }
            }
        }

        // DEACTIVATE ACTION
        if(isset($_POST['deactivate'])) { 
            $keyDomain = unserialize(get_option($this->pluginSlug . self::PLUGIN_KEY));
            if(false !== $keyDomain) {

                $response = wp_remote_post(self::API_DOMAIN . self::DEACTIVATE_POINT, [
                    'body' => [
                        'key'         => $keyDomain['key'],
                        'domain'      => $keyDomain['domain'],
                        'plugin_slug' => $this->pluginSlug,
                    ]
                ]);

                $responseDecoded = json_decode($response['body'], true);

                if(isset($responseDecoded['plugin']) && $responseDecoded['plugin'] == 'deactivated') {
                    delete_option($this->pluginSlug . self::PLUGIN_KEY);
                    header("Refresh:1");
                } elseif(isset($responseDecoded['error'])) {
                    $errors = $responseDecoded['error'];
                } else {
                    $errors = __( 'Something went wrong.', 'ws-plugin-authentication' );
                }

                header("Refresh:1");
            }
        }

        load_template(__DIR__ . '/../templates/license.php', true, [
            'errors' => $errors,
            'keyDomain' => $keyDomain,
            'pluginName' => $this->pluginName,
            'assetsUrl' => plugins_url() . '/' . $this->pluginSlug . '/vendor/websystemspl/plugin-authentication/assets'
        ]);
    }

    /**
     * Add assets to admin
     *
     * @return void
     */
    public function adminAssets(): void
    {
        wp_enqueue_style( 'ws-plugin-authentication-admin-styles', plugins_url() . '/' . $this->pluginSlug . '/vendor/websystemspl/plugin-authentication/assets/css/ws-plugin-authentication-styles.css' );
    }    
}
