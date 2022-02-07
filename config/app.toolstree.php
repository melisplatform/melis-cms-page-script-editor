<?php

/**
 * Melis Technology (http://www.melistechnology.com]
 *
 * @copyright Copyright (c] 2015 Melis Technology (http://www.melistechnology.com]
 *
 */

return [
    'plugins' => [        
        //page edition script tab
        'meliscmspagescripteditor' => [
            'interface' => [
                /*page edition tab*/
                'meliscmspagescripteditor_page_edition' => [
                    'conf' => [
                        'id' => 'id_meliscms_page_script_editor',
                        'melisKey' => 'meliscms_page_script_editor',
                        'name' => 'tr_meliscmspagescripteditor_title',
                        'icon' => 'glyphicons embed_close',                       
                    ],
                    'forward' => [
                        'module' => 'MelisCmsPageScriptEditor',
                        'controller' => 'MelisCmsPageScriptEditorPageEdition',
                        'action' => 'render-page-script-editor',
                        'jscallback' => '',
                        'jsdatas' => []
                    ],
                    'interface' => [
                        'meliscmspagescripteditor_script_form' => [
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
                    ]
                ],//end meliscmspagescripteditor_page_edition
            ],
        ],

        //site tool script tab
        'meliscmssitetoolscripteditor' => [            
            'interface' => [
                'meliscms_tool_sites_scripts' => [
                    'conf' => [
                        'id' => 'id_meliscms_tool_sites_scripts',
                        'melisKey' => 'meliscms_tool_sites_scripts',
                        'name' => 'tr_meliscmspagescripteditor_title',
                        'icon' => 'glyphicons embed_close',
                    ],
                    'forward' => [
                        'module' => 'MelisCmsPageScriptEditor',
                        'controller' => 'MelisCmsPageScriptEditorToolSiteEdition',
                        'action' => 'render-tool-site-scripts',
                        'jscallback' => '',
                        'jsdatas' => array()
                    ],
                    'interface' => [
                        'meliscms_tool_sites_script_content' => [
                            'conf' => [
                                'id' => 'id_meliscms_tool_sites_script_content',
                                'melisKey' => 'meliscms_tool_sites_script_content',
                                'name' => 'tr_melis_cms_sites_tool_content_edit_script_tab_content',
                                'rightsDisplay' => 'true',
                            ],
                            'forward' => [
                                'module' => 'MelisCmsPageScriptEditor',
                                'controller' => 'MelisCmsPageScriptEditorToolSiteEdition',
                                'action' => 'render-tool-site-script-content',
                                'jscallback' => '',
                                'jsdatas' => array()
                            ],
                            'interface' => [
                                 'meliscms_tool_sites_script_exceptions' => [
                                    'conf' => [
                                        'id' => 'id_meliscms_tool_sites_script_exceptions',
                                        'melisKey' => 'meliscms_tool_sites_script_exceptions',
                                        'name' => 'tr_meliscms_tool_sites_script_exceptions',
                                    ],
                                    'forward' => [
                                        'module' => 'MelisCmsPageScriptEditor',
                                        'controller' => 'MelisCmsPageScriptEditorToolSiteEditionController',
                                        'action' => 'render-script-exceptions',
                                        'jscallback' => '',
                                        'jsdatas' => [],
                                    ],   
                                ],
                            ]
                        ],
                    ],
                ],
            ],
        ],
        
        'meliscms' => [       
            'interface' => [
                //for the page edition
                'meliscms_page' => [
                    'interface' => [
                        'meliscms_tabs' => [
                            'interface' => [
                                'meliscmspagescripteditor_page_script_tab' => [
                                    'conf' => [
                                        'type' => '/meliscmspagescripteditor/interface/meliscmspagescripteditor_page_edition',
                                    ],                                   
                                ],                               
                            ],
                        ],
                    ],
                ],  

                //for the site tool edition
                'meliscms_toolstree' => [
                    'interface' => [
                        'meliscms_tool_sites_edit_site' => [
                            'interface' =>  [
                                'meliscms_tool_sites_edit_site_tabs' => [
                                    'interface' =>  [
                                        'meliscms_tool_sites_edit_site_tabs_script' => [
                                            'conf' => [
                                                'type' => 'meliscmssitetoolscripteditor/interface/meliscms_tool_sites_scripts'
                                            ]
                                        ],
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