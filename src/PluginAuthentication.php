<?php

namespace websystemspl;

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
    public function boot()
    {
        add_action('admin_menu', [$this, 'createAdminMenuPage'], 1);
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
    public function createAdminMenuPage()
    {
        if(null !== $this->adminMenuPageParent) {
            add_submenu_page( 
                $this->adminMenuPageParent,
                __( 'License - ' . $this->pluginName, 'ws-plugin-authentication' ), 
                __( 'License - ' . $this->pluginName, 'ws-plugin-authentication' ),
                'manage_options',
                'dashboard',
                [$this, 'adminMenuPageHandler'],
                10
            );             
        }
        add_menu_page(
            __( 'License - ' . $this->pluginName, 'ws-plugin-authentication' ), 
            __( 'License - ' . $this->pluginName, 'ws-plugin-authentication' ),
            'manage_options',
            'ws-plugin-authentication-page',
            [$this, 'adminMenuPageHandler'],
            'dashicons-admin-network',
            10
        );
    }

    /**
     * Listen for updates
     *
     * @return void
     */
    private function updateListener()
    {
        $keyDomain = unserialize(get_option($this->pluginSlug . self::PLUGIN_KEY));
        if(false !== $keyDomain) {
            \Puc_v4_Factory::buildUpdateChecker(
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
    public function adminMenuPageHandler()
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
                    header("Refresh:0");
                } elseif(isset($responseDecoded['error'])) {
                    $errors = $responseDecoded['error'];
                } else {
                    $errors = 'Something went wrong.';
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
                    header("Refresh:0");
                } elseif(isset($responseDecoded['error'])) {
                    $errors = $responseDecoded['error'];
                } else {
                    $errors = 'Something went wrong.';
                }

                header("Refresh:0");
            }
        }

        load_template(__DIR__ . '/../templates/license.php', true, [
            'errors' => $errors,
            'keyDomain' => $keyDomain
        ]);
    }
}