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
            'interface' => [
                /*page edition tab*/
                'meliscmspagescripteditor_page_edition' => [
                    'conf' => [
                        'id' => 'id_meliscmspagescripteditor_tool',
                        'melisKey' => 'meliscmspagescripteditor_tool',
                        'name' => 'tr_meliscmspagescripteditor_title',
                        'icon' => 'glyphicons embed_close',
                        // 'follow_regular_rendering' => false,
                    ],
                    'forward' => [
                        'module' => 'MelisCmsPageScriptEditor',
                        'controller' => 'MelisCmsPageScriptEditorPageEdition',
                        'action' => 'render-page-script-editor',
                        'jscallback' => '',
                        'jsdatas' => []
                    ],
                    'interface' => [
                        'meliscmspagescripteditor_launch_form' => [
                            'conf' => [
                                'id' => 'id_meliscmspagescripteditor_header',
                                'melisKey' => 'meliscmspagescripteditor_header',
                                'name' => 'tr_meliscmspagescripteditor_header',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsPageScriptEditor',
                                'controller' => 'MelisCmsPageScriptEditorPageEdition',
                                'action' => 'render-page-script-editor-launch-form', 
                                'jscallback' => '',
                                'jsdatas' => []
                            ],
                        ],
                        // 'meliscmspagescripteditor_content' => [
                        //     'conf' => [
                        //         'id' => 'id_meliscmspagescripteditor_content',
                        //         'melisKey' => 'meliscmspagescripteditor_content',
                        //         'name' => 'tr_meliscmspagescripteditor_content',
                        //     ],
                        //     'forward' => [
                        //         'module' => 'MelisCmsPageScriptEditor',
                        //         'controller' => 'List',
                        //         'action' => 'render-tool-content',
                        //         'jscallback' => '',
                        //         'jsdatas' => []
                        //     ],
                        //     'interface' => [

                        //     ]
                        // ]
                    ]
                ]
            ],
        ],

        //for the page edition
        'meliscms' => [       
            'interface' => [
                'meliscms_page' => [
                    'interface' => [
                        'meliscms_tabs' => [
                            'interface' => [
                                'meliscmspagescripteditor_script_tab' => [
                                    'conf' => [
                                        'type' => '/meliscmspagescripteditor/interface/meliscmspagescripteditor_page_edition',
                                    ],                                   
                                ],                               
                            ],
                        ],
                    ],
                ],                
            ],
        ],

    ]
];