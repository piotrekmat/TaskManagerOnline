<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return [
    'router' => [
        // /*
        'routes' => [
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            ///* 
            'taskmanager' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/taskmanager',
                    'defaults' => [
                        '__NAMESPACE__' => 'Taskmanager\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/[:controller]/[:action]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                        ],
                        'child_routes' => [
                            'wilcard' => [
                                'type' => 'Wildcard'
                            ]
                        ],
                    ],
                ],
            ],
        ],
    //
    ],
    'controllers' => [
        'invokables' => [
            'Taskmanager\Controller\Index' => 'Taskmanager\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
