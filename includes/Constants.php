<?php
// Paths on the server
define('BASE_PATH', realpath(__DIR__ . '/../') . '/');
define('MODULES_DIR', BASE_PATH . 'modules/');
define('INCLUDES_DIR', BASE_PATH . 'includes/');
define('SERVICES_DIR', BASE_PATH . 'services/');
define('CONFIGS_DIR', BASE_PATH . 'config/');
define('VIEWS_DIR', BASE_PATH . 'views/');

define('I18N_DIR', BASE_PATH . 'i18n/');
define('I18N_POT', I18N_DIR . 'i18n.pot');

define('TMP_DIR', BASE_PATH . 'tmp/');
define('CACHE_DIR', TMP_DIR . 'cache/');
define('LOG_DIR', BASE_PATH . 'logs/');

define('VIEWS_CACHE_DIR', CACHE_DIR . 'views/');
define('I18N_CACHE_DIR', CACHE_DIR . 'i18n/');
define('CONFIG_CACHE_PATH', CACHE_DIR . 'config.php');

define('APP_CONFIG_PATH', CONFIGS_DIR . 'app.json');
define('I18N_DEFAULT_LANG', 'en');
define('COMPOSER_PATH', BASE_PATH . 'vendor/autoload.php');
define('LOG_PATH', LOG_DIR . date('Y-m-d') . '.log');

// Paths as they seen from the web
define('CSS_WEB_DIR', '/css');
define('JS_WEB_DIR', '/js');

// Other constants
define('SESSION_COOKIE_NAME', 'PHPSESSID');
define('TOKEN_COOKIE_NAME', 'token');
define('DT_FORMAT', 'c');
