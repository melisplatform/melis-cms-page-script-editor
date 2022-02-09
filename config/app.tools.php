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
            'tools' => [
                'meliscmspagescripteditor_site_script_exceptions' => [
                    'conf' => [
                        'title' => 'tr_meliscmspagescripteditor_site_script_exceptions',
                        'id' => 'id_meliscmspagescripteditor_site_script_exceptions',
                    ],
                    'table' => [
                        // table ID
                        'target' => '#MelisCmsPageScriptEditorScriptExceptionsTable',
                        'ajaxUrl' => '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorToolSiteEdition/getScriptExceptions',
                        'dataFunction' => 'initSiteId',
                        'ajaxCallback' => '',
                        'data' => [                                                                     
                            'columnDefs' => [                                
                                ['targets' => [0], 'visible' => false],                                                               
                            ],    
                            'autoWidth' => false                         
                        ],
                        'attributes' => [
                            'id' => '',
                            'class' => 'table table-striped table-primary dt-responsive nowrap',
                            'cellspacing' => '0',
                            'width' => '100%'
                         ],
                         'filters' => [
                            'left' => [                                
                            ],
                            'center' => [                                
                            ],
                            'right' => [                                
                            ],
                        ],
                        'columns' => [    
                            'mcse_id' => [
                                'text' => 'tr_meliscmspagescripteditor_exception_id',
                                'css' => array('width' => '15%', 'padding-right' => '0'),
                                'sortable' => false                           
                            ],                       
                            'mcse_page_id' => [
                                'text' => 'tr_meliscmspagescripteditor_page_id',
                                'css' => array('width' => '15%', 'padding-right' => '0'),
                                'sortable' => false                           
                            ],
                            'page_name' => [
                                'text' => 'tr_meliscmspagescripteditor_page_name',
                                'css' => array('width' => '65%', 'padding-right' => '0'),
                                'sortable' => false                                     
                            ],                                                     
                        ],
                        // define what columns can be used in searching
                        'searchables' => [
                        ],
                        'actionButtons' => [
                            'delete_exception' => [
                                  'module' => 'MelisCmsPageScriptEditor',
                                  'controller' => 'MelisCmsPageScriptEditorToolSiteEdition',
                                  'action' => 'render-table-action-delete-exception',
                            ],
                        ]
                    ],
                ]
            ],//end tools

            'forms' => [
                //used in the Page Edition and Tool Site's Script Tab
                'meliscmspagescripteditor_script_form' => [
                    'attributes' => [
                        'name' => 'page-script-editor',
                        'id' => 'page-script-editor',
                        'class' => 'page-script-editor',
                        'method' => 'POST'                       
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'mcs_id',
                                'type' => 'hidden',                                
                                'attributes' => [
                                    'id' => 'mcs_id',
                                    'value' => '',                                   
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'mcs_head_top',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmspagescripteditor_head_top',
                                    'tooltip' => 'tr_meliscmspagescripteditor_head_top tooltip',
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                ],
                                'attributes' => [
                                    'id' => 'mcs_head_top',
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ],
                            ],
                        ], 
                        [
                            'spec' => [
                                'name' => 'mcs_head_bottom',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmspagescripteditor_head_bottom',
                                    'tooltip' => 'tr_meliscmspagescripteditor_head_bottom tooltip',
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                ],
                                'attributes' => [
                                    'id' => 'mcs_head_bottom',
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ],
                            ],
                        ], 
                        [
                            'spec' => [
                                'name' => 'mcs_body_bottom',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmspagescripteditor_body_bottom',
                                    'tooltip' => 'tr_meliscmspagescripteditor_body_bottom tooltip',
                                    'label_options' => [
                                        'disable_html_escape' => true,
                                    ],
                                ],
                                'attributes' => [
                                    'id' => 'mcs_body_bottom',
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control',
                                    'rows' => 4
                                ],
                            ],
                        ],                                       

                    ],
                    'input_filter' => [
                        'mcs_head_top' => [
                            'name'     => 'mcs_head_top',
                            'required' => false,
                            'validators' => [    
                               
                            ],
                            'filters'  => [                              
                                ['name' => 'StringTrim'],                               
                            ],
                        ],                        
                        'mcs_head_bottom' => [
                            'name'     => 'mcs_head_bottom',
                            'required' => false,
                            'validators' => [
                                                          
                            ],
                            'filters'  => [                               
                                ['name' => 'StringTrim'],
                            ],
                        ],
                        'mcs_body_bottom' => [
                            'name'     => 'mcs_body_bottom',
                            'required' => false,
                            'validators' => [                                                
                            ],
                            'filters'  => [
                                ['name' => 'StringTrim'],
                            ],
                        ],                       
                    ],
                ], 

                //used in the Page Edition's Script Tab
                'meliscmspagescripteditor_script_exception_form' => [
                    'attributes' => [
                        'name' => 'page-script-editor-exception',
                        'id' => 'page-script-editor-exception',
                        'class' => 'page-script-editor-exception',
                        'method' => 'POST'                       
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'mcse_id',
                                'type' => 'hidden',                                
                                'attributes' => [
                                    'id' => 'mcse_id',
                                    'value' => '',                                   
                                ],
                            ],
                        ],
                        [
                            'spec' => [
                                'name' => 'mcse_exclude_site_scripts',
                                'type' => 'checkbox',
                                'options' => [
                                    'label' => 'tr_meliscmspagescripteditor_exclude_site_script',
                                    'tooltip' => '',
                                    'use_hidden_element' => true,
                                ],
                                'attributes' => [
                                    'id' => 'mcse_exclude_site_scripts',
                                    'value' => '',
                                    'placeholder' => '',
                                    'class' => 'form-control'                                    
                                ],
                            ],
                        ], 
                    ],
                    'input_filter' => [
                        'mcse_exclude_site_scripts' => [
                            'name'     => 'mcse_exclude_site_scripts',
                            'required' => false,
                            'validators' => [                                   
                            ],
                            'filters'  => [      
                                ['name' => 'StripTags'],                        
                                ['name' => 'StringTrim'],
                            ],
                        ],          
                    ],
                ], 

                /*for adding/deleting of exception in the tool site*/
                'meliscmspagescripteditor_tool_site_exception_form' => [
                    'attributes' => [
                        'name' => 'page-script-editor-tool-site-exception',
                        'id' => 'page-script-editor-tool-site-exception',
                        'class' => 'page-script-editor-tool-site-exception',
                        'method' => 'POST'                       
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'tool_site_mcse_page_id',
                                'type' => 'hidden',      
                                'options' => [
                                    'label' => 'tr_meliscmspagescripteditor_page_id',
                                ],                          
                                'attributes' => [
                                    'id' => 'tool_site_mcse_page_id',
                                    'value' => '',                                   
                                ],
                            ],
                        ],                     
                    ],
                    'input_filter' => [
                        'tool_site_mcse_page_id' => [
                            'name' => 'tool_site_mcse_page_id',
                            'required' => true,
                            'validators' => [
                                [
                                    'name' => 'NotEmpty',
                                    'break_chain_on_failure' => true,
                                    'options' => [
                                        'messages' => [
                                            \Laminas\Validator\NotEmpty::IS_EMPTY => 'tr_meliscmspagescripteditor_err_empty',
                                        ],
                                    ],
                                ],    
                                [
                                'name' => 'IsInt',
                                    'options' => [
                                        'messages' => [
                                            \Laminas\I18n\Validator\IsInt::NOT_INT  => 'tr_meliscmspagescripteditor_integer_only'
                                        ],                                                                       
                                    ],
                                ],                                                                  
                            ],
                            'filters'  => [      
                                ['name' => 'StripTags'],                        
                                ['name' => 'StringTrim'],
                            ],
                        ],          
                    ],
                ], 
            ],//end forms
        ]
    ]
];