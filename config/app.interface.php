<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [
        'meliscmspagescripteditor' => [
            'conf' => [
                'id' => '',
                'name' => 'tr_meliscmspagescripteditor_tool_name',
                'rightsDisplay' => 'none',
            ],
            'ressources' => [
                'js' => [
                   '/MelisCmsPageScriptEditor/js/tool.js'
                ],
                'css' => [
                    '/MelisCmsPageScriptEditor/css/custom.css'
                ],
                /**
                 * the "build" configuration compiles all assets into one file to make
                 * lesser requests
                 */
                'build' => [
                    // configuration to override "use_build_assets" configuration, if you want to use the normal assets for this module.
                    'disable_bundle' => false,
                    // lists of assets that will be loaded in the layout
                    'css' => [
                        '/MelisCmsPageScriptEditor/build/css/bundle.css',
                    ],
                    'js' => [
                        '/MelisCmsPageScriptEditor/build/js/bundle.js',
                    ]
                ]
            ],
            'datas' => [                
            ],
        ]
    ]
];