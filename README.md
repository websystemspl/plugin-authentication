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

$pluginAuthentication = new websystemspl\PluginAuthentication('NAME OF YOUR PLUGIN', 'SLUG OF YOUR PLUGIN', 'ADMIN PAGE PARENT', 'BACKTRACK FILE INDEX');

$pluginAuthentication->boot();

- ADMIN PAGE PARENT - Not necessary but if you want to show activate page as subpage of plugin menu settings then add.
- BACKTRACK FILE INDEX - optional parameter (integer) indicating the index of the file in backtrace(). It is set to 0 by default, which means the file where the class instance is created should be the main file of the plugin or the theme's functions.php file.