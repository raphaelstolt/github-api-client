<?php
/**
 * Determine the root directory of the framework component.
 */
$componentPath = realpath(dirname(dirname(__FILE__)));
set_include_path(get_include_path() . PATH_SEPARATOR . $componentPath);
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
/**
 * Online test configuration
 */
defined('TESTS_ZEND_SERVICE_GITHUB_ONLINE_ENABLED')
    || define('TESTS_ZEND_SERVICE_GITHUB_ONLINE_ENABLED', true);
defined('TESTS_ZEND_SERVICE_GITHUB_USER')
    || define('TESTS_ZEND_SERVICE_GITHUB_USER', 'zfghclient');
defined('TESTS_ZEND_SERVICE_GITHUB_API_TOKEN')
    || define('TESTS_ZEND_SERVICE_GITHUB_API_TOKEN', '8de326273b0228faf5ec44578364ae6c');
?>