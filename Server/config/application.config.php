<?php

return [
    'modules' => [
#         'SmartyModule',
        'ZendDeveloperTools',
        'Application',
        'Webservice',
        'Taskmanager'
    ],
    'module_listener_options' => [
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            './module',
            './vendor',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation' => '\Zend\Navigation\Service\DefaultNavigationFactory',
            'menu' => '\Zend\Navigation\Service\DefaultNavigationFactory',
        ],
    ],
];
