<?
    if ( !defined("_NB_VALID_") ) exit;

    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    define("_NB_ROOT_", __DIR__ . "/..");

    // Include Necessary Libraries
    session_start();

    require_once(_NB_ROOT_ . "/system/context.php");
    require_once(_NB_ROOT_ . "/system/security.php");
    require_once(_NB_ROOT_ . "/system/database.php");

    // Class Autoloader Configuration
    spl_autoload_register(function ($class) {
        $search_list = [
            _NB_ROOT_ . "/controllers/",
            _NB_ROOT_ . "/system/classes/",
            _NB_ROOT_ . "/system/interfaces/"
        ];

        foreach ($search_list as $dir) {
            if (file_exists($dir . "$class.php")) {
                require_once($dir . "$class.php");
                return true;
            }
        }

        return false;
    });

    // Begin Routing
    $context = new Context();

    $controller = lcfirst($context->url_frag(0) ?? "main") . "_controller";
    $action     = lcfirst($context->url_frag(1) ?? "index");

    response::dispatch_action($context, $controller, $action)
            ->emit();