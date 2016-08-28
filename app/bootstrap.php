<?php

// Application root directory
define('APP_BASE_DIR', __DIR__ . '/../');
// Application base url
define('APP_BASE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['CONTEXT_PREFIX'] . '/');
define('APP_VERSION', 0.1);

require_once APP_BASE_DIR . 'app/Configuration.php';
?>
