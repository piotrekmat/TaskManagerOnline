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
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/dashboard',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
// new controllers and actions without needing to create a new
// module. Simply drop new controllers in, and you can access them
// using the path /application/:controller/:action
            'default' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ],
        'abstract_factories' => ['Zend\Navigation\Service\NavigationAbstractServiceFactory']
    ],
    'translator' => [
        'locale' => 'pl_PL',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language/',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ],
    ],
    'controller_plugins' => [
        'invokables' => [
            'params' => '\Application\Controller\Plugin\Params',
        ],
    ],
    'view_manager' => [
        'defaultSuffix' => 'tpl',
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => 'view/layout/layout.tpl',
            //'application/index/index' => 'view/application/index/index.phtml',
            'error/404' => 'view/error/404.tpl',
            'error/index' => 'view/error/index.tpl',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
//    'navigation' => [
//        'default' => [
//            [
//                'label' => 'Home',
//                'title' => 'Go Home',
//                'module' => 'cms',
//                'controller' => 'index',
//                'action' => 'index',
//                'type' => 'Application\Navigation\Page\Route',
//                'order' => -100 // make sure home is the first page
//            ]
//        ]
//    ]
];



