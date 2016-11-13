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
            'weservice' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route' => '/soap',
                    'defaults' => [
                        'controller' => 'Webservice\Controller\Soap',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/[:soapcontroller][/[:soapaction]]',
                            'constraints' => [
                                'scontroller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'saction' => '[a-zA-Z][a-zA-Z0-9_-]*',
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
    ],
    'controllers' => [
        'invokables' => [
            'Webservice\Controller\Soap' => 'Webservice\Controller\SoapController',
        ],
    ],
//    'view_manager' => [
//        'template_path_stack' => [
//            __DIR__ . '/../view',
//        ],
//    ],
];
