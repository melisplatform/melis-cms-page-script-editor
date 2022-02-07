<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'router' => [
        'routes' => [
        	'melis-backoffice' => [
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/melis[/]',
                ),
                'child_routes' => [
                    'application-MelisCmsPageScriptEditor' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => 'MelisCmsPageScriptEditor',
                            'defaults' => [
                                '__NAMESPACE__' => 'MelisCmsPageScriptEditor\Controller',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'default' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/[:controller[/:action]]',
                                    'constraints' => [
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ],
                                    'defaults' => [
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
        	],
        ],
    ],
    'service_manager' => [
         'aliases' => [
            // Service
            'MelisCmsPageScriptEditorService' => \MelisCmsPageScriptEditor\Service\MelisCmsPageScriptEditorService::class,           
            // Tables
            'MelisCmsScriptTable' => \MelisCmsPageScriptEditor\Model\Tables\MelisCmsScriptTable::class,
            'MelisCmsScriptExceptionTable' => \MelisCmsPageScriptEditor\Model\Tables\MelisCmsScriptExceptionTable::class
        ]
    ],
    'view_helpers' => [        
        'invokables' => [
            'melisCmsPageScriptEditorAddScript' => \MelisCmsPageScriptEditor\View\Helper\MelisCmsPageScriptEditorAddScriptHelper::class
        ],
    ],
    'controllers' => [
        'invokables' => [          
            'MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorPageEdition' => \MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorPageEditionController::class, 
            'MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorToolSiteEdition' => \MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorToolSiteEditionController::class, 
        ],
    ],
    'view_manager' => [
        'template_map' => [
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
