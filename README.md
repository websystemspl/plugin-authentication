# plugin-authentication

Add to your composer.json file:

    "repositories":[
        {
            "type": "vcs",
            "url": "https://github.com/websystemspl/plugin-authentication.git"
        }
    ]  

and:

    "require": {
        "websystemspl/plugin-authentication": "dev-main"
    },

Create object on init plugin:

$pluginAuthentication = new websystemspl\PluginAuthentication('NAME OF YOUR PLUGIN', 'SLUG OF YOUR PLUGIN', 'ADMIN PAGE PARENT');

$pluginAuthentication->boot();

ADMIN PAGE PARENT - Not necessary but if you want to show activate page as subpage of plugin menu settings then add.