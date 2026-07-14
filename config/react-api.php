<?php

/** Route react-api de l'onglet Scripts — MODULAIRE. Mergé via MelisCmsPageScriptEditor\Module::getConfig(). */

return [
    'router' => [
        'routes' => [
            'melis-backoffice' => [
                'child_routes' => [
                    'melis-react-api' => [
                        'child_routes' => [
                            'cms-page-scripts' => [
                                'type'    => 'Segment',
                                'options' => [
                                    'route'    => '/cms-page/scripts[/]',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'MelisCmsPageScriptEditor\Controller',
                                        'controller'    => 'MelisReactApiPageScriptEditor',
                                        'action'        => 'get',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'MelisCmsPageScriptEditor\Controller\MelisReactApiPageScriptEditor' => \MelisCmsPageScriptEditor\Controller\MelisReactApiPageScriptEditorController::class,
        ],
    ],
];
