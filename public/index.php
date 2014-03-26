<?php
error_reporting(E_ALL ^ E_NOTICE);
$HOST = $_SERVER['SERVER_NAME'];
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('PUBLIC_PATH') || define('PUBLIC_PATH', "");

defined('APP_PATH') || define('APP_PATH', "http://{$HOST}/application");

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH. '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

Zend_Session::start(array('gc_maxlifetime' => 864000, 'cookie_lifetime' => 864000));

$application->bootstrap()->run();