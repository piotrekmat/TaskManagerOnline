<?php

namespace Webservice;


return [
	'controllers' => [
		'invokables' => [
			'Webservice\Controller\Index'         => 'Webservice\Controller\IndexController',
		],
		'factories'=> [
//			'Webservice\Controller\IdealoController'  => 'Webservice\Factory\IdealoControllerFactory',
		],
	],
	'router' => [
		'routes' => [
			'webservice' => [
				'type' => 'segment',
				'options' => [
					'route' => '/soap',
					'defaults' => [
						'controller' => 'Webservice\Controller\Index',
						'action'     => 'index',
					],
				],
				'child_routes' => [
					'index' => [
						'type' => 'segment',
						'options' => [
							'route' => '/index/:action[/:id]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[a-z0-9]+',
							],
							'defaults' => [
								'controller' => 'webservice/index',
								'action'     => 'index',
								'id'         => null,
							],
						],
					],
					'action' => [
						'type'    => 'segment',
						'options' => [
							'route'       => '/action/:action[/:id]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[a-z0-9]+',
							],
							'defaults'    => [
								'controller' => 'webservice/action',
								'action'     => 'index',
								'id'         => null,
							],
						],
					],
					'action-europe' => [
						'type' => 'segment',
						'options' => [
							'route' => '/action-europe/:action[/:id]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[a-z0-9]+',
							],
							'defaults' => [
								'controller' => 'webservice/action-europe',
								'action'     => 'index',
								'id'         => null,
							],
						],
					],
					'order' => [
						'type' => 'segment',
						'options' => [
							'route' => '/order/:action[/:id]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id'     => '[a-z0-9]+',
							],
							'defaults' => [
								'controller' => 'webservice/order',
								'action'     => 'index',
								'id'         => null,
							],
						],
					],
					'google' => [
						'type' => 'segment',
						'options' => [
							'route' => '/google[/:action[/:id]]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[a-zA-Z0-9-\+]*',
							],
							'defaults' => [
								'controller' => 'webservice/google',
								'action' => 'index',
								'id' => null,
							],
						],
					],
					'idealo' => [
						'type' => 'segment',
						'options' => [
							'route' => '/idealo[/:action[/:id]]',
							'constraints' => [
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[a-zA-Z0-9-\+]*',
							],
							'defaults' => [
								'controller' => 'Webservice\Controller\IdealoController',
								'action' => 'index',
								'id' => null,
							],
						],
					],
				],
				'may_terminate' => true,
			],
		],
	],

	'console' => [
		'router' => [
			'routes' => [
				'action update warehouse cache' => [
					'options' => [
						'route'    => 'action update warehouse cache [--verbose=]',
						'defaults' => [
							'controller' => 'webservice/action',
							'action'     => 'updateWarehouseCache',
						],
					],
				],
				'action add new products' => [
					'options' => [
						'route'    => 'action add new products [--verbose=]',
						'defaults' => [
							'controller' => 'webservice/action',
							'action'     => 'addNewProducts',
						],
					],
				],
				'action europe update warehouse cache' => [
					'options' => [
						'route'    => 'action europe update warehouse cache [--verbose=]',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'updateActionEuropeWarehouseCache',
						],
					],
				],
				'action europe reserve orders' => [
					'options' => [
						'route'    => 'action europe reserve orders',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'reserveOrders',
						],
					],
				],
				'action europe send reservation mail' => [
					'options' => [
						'route'    => 'action europe send reservation mail',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'sendReservationMail',
						],
					],
				],
				'action europe send cancellation mail' => [
					'options' => [
						'route'    => 'action europe send cancellation mail',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'sendCancellationMail',
						],
					],
				],
				'action europe process orders' => [
					'options' => [
						'route'    => 'action europe process orders',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'processActionEuropeOrders',
						],
					],
				],
				'action europe process-order-feed' => [
					'options' => [
						'route'    => 'action europe process order feed <feedId> [--verbose=]',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'processOrderFeed',
						],
					],
				],
				'action europe test' => [
					'options' => [
						'route'    => 'action europe test [--verbose=]',
						'defaults' => [
							'controller' => 'webservice/action-europe',
							'action'     => 'test',
						],
					],
				],
				'action generate google shopping xml' => [
					'options' => [
						'route'    => 'action generate google shopping xml',
						'defaults' => [
							'controller' => 'webservice/google',
							'action'     => 'generateGoogleShoppingXML',
						],
					],
				],
				'idealo prepare pricesetter file' => [
					'options' => [
						'route'    => 'idealo prepare pricesetter file [--save=]',
						'defaults' => [
							'controller' => 'Webservice\Controller\IdealoController',
							'action'     => 'prepareFile',
						],
					],
				],
			],
		],
	],
	
	'view_manager' => [
		'template_path_stack' => [
			__DIR__ . '/../view',
		],
	],
	'doctrine' => [
		'driver' => [
			'application_entities' => [
				'paths' => [
					__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
				],
			],
			'orm_default' => [
				'drivers' => [
					__NAMESPACE__ . '\Entity' => 'application_entities'
				],
			],
		],
	],
	'asset_manager' => [
		'resolver_configs' => [
			'paths' => [
				'pdf' => __DIR__ . '/../public',
			],
		],
	],
];

