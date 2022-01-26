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
            // 'tools' => [
            //     'meliscmspagescripteditor_tools' => [
            //         'conf' => [
            //             'title' => 'tr_meliscmspagescripteditor_templates',
            //             'id' => 'id_meliscmspagescripteditor_templates',
            //         ],
            //         'table' => [
            //             // table ID
            //             'target' => '#tableToolMelisCmsPageScriptEditor',
            //             'ajaxUrl' => '/melis/MelisCmsPageScriptEditor/List/getList',
            //             'dataFunction' => '',
            //             'ajaxCallback' => '',
            //             'filters' => [
            //                 'left' => [
            //                     'meliscmspagescripteditor-tbl-filter-limit' => [
            //                         'module' => 'MelisCmsPageScriptEditor',
            //                         'controller' => 'List',
            //                         'action' => 'render-table-filter-limit',
            //                     ],
            //                 ],
            //                 'center' => [
            //                     'meliscmspagescripteditor-tbl-filter-search' => [
            //                         'module' => 'MelisCmsPageScriptEditor',
            //                         'controller' => 'List',
            //                         'action' => 'render-table-filter-search',
            //                     ],
            //                 ],
            //                 'right' => [
            //                     'meliscmspagescripteditor-tbl-filter-refresh' => [
            //                         'module' => 'MelisCmsPageScriptEditor',
            //                         'controller' => 'List',
            //                         'action' => 'render-table-filter-refresh',
            //                     ],
            //                 ],
            //             ],
            //             'columns' => [

            //             ],
            //             // define what columns can be used in searching
            //             'searchables' => [

            //             ],
            //             'actionButtons' => [

            //             ]
            //         ],

            //     ]
            // ],//end tools

            'forms' => [
                'meliscmspagescripteditor_script_form' => [
                    'attributes' => [
                        'name' => 'page-script-editor',
                        'id' => 'page-script-editor',
                        'class' => 'page-script-editor',
                        'method' => 'POST',
                        'action' => '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorPageEdition/saveScript'
                    ],
                    'hydrator'  => 'Laminas\Hydrator\ArraySerializable',
                    'elements' => [
                        [
                            'spec' => [
                                'name' => 'mcs_head_top',
                                'type' => 'Textarea',
                                'options' => [
                                    'label' => 'tr_meliscmspagescripteditor_head_top',
                                    'tooltip' => 'tr_meliscmspagescripteditor_head_top tooltip',
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

            ],//end forms
        ]
    ]
];