<?php

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');

chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
\Zend\Mvc\Application::init(require 'config/application.config.php')->run();
