<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2017 Melis Technology (http://www.melistechnology.com)
 *
 */
namespace MelisCmsPageScriptEditor\Controller;

use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use MelisCore\Controller\MelisAbstractActionController;

class MelisCmsPageScriptEditorToolSiteEditionController extends MelisAbstractActionController
{
    // The form is loaded from the app.tools array
    const PageScriptAppConfigPath = '/meliscmspagescripteditor/forms/meliscmspagescripteditor_script_form';
    const PageScriptToolSiteExceptionAppConfigPath = '/meliscmspagescripteditor/forms/meliscmspagescripteditor_tool_site_exception_form';
    
    /* Renders the script tab
     * @return \Laminas\View\Model\ViewModel
    */    
    public function renderToolSiteScriptsAction()
    {    
        $siteId = (int) $this->params()->fromQuery('siteId', '');
       
        $rightService = $this->getServiceManager()->get('MelisCoreRights');
        $canAccess = $rightService->canAccess('meliscms_tool_sites_script_content');

        $view = new ViewModel();
        $view->siteId = $siteId;
        $view->canAccess = $canAccess;
        return $view;
    }

    /* Renders the script tab content
     * @return \Laminas\View\Model\ViewModel
    */  
    public function renderToolSiteScriptContentAction()
    {
        $siteId = (int) $this->params()->fromQuery('siteId', '');

        /**
         * Make sure site id is not empty
         */
        if (empty($siteId))
            return;

        //get form
        $scriptForm = $this->getSiteScriptForm($siteId);

        // get site script data if there are any
        $scriptTable = $this->getServiceManager()->get('MelisCmsScriptTable'); 
        $siteScript = $scriptTable->getEntryByField('mcs_site_id', $siteId)->current();
     
        // bind data to form
        if ($siteScript) {
            $scriptForm->bind($siteScript);
        }             

        //get tool site exception form
        $scriptExceptionForm = $this->getSiteScriptExceptionForm($siteId);

        $view = new ViewModel();
        $view->siteId = $siteId;
        $view->scriptForm = $scriptForm;     
        $view->scriptExceptionForm = $scriptExceptionForm;       
        return $view;
    }
    
    /* Saves the script to DB
     * @return \Laminas\View\Model\JsonModel
    */
    public function saveSiteScriptAction()
    {              
        $siteId = $this->params('siteId');   
               
        $eventDatas = array('siteId' => $siteId);
        $this->getEventManager()->trigger('meliscms_site_save_script_start', null, $eventDatas);
        $scriptSuccess = 1;
        $exceptionSuccess = 1;
        $scriptErrors = [];
        $exceptionErrors = [];
                
        // Check if post
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Get values posted and set them in form
            $postValues = $request->getPost()->toArray();

            // Get the form 
            $scriptForm = $this->getSiteScriptForm($siteId);
            $scriptForm->setData($postValues);

            // Validate the form
            if ($scriptForm->isValid()) {
                // Get datas validated
                $scriptData = $scriptForm->getData();

                //use helper to add the scripts to DB
                $viewHelperManager = $this->getServiceManager()->get('ViewHelperManager');
                $addScriptHelper = $viewHelperManager->get('melisCmsPageScriptEditorAddScript');      
                
                //set page id to null  
                $pageId = null;        
                $scriptSuccess = $addScriptHelper->addScriptData($this->getServiceManager(), $scriptData, $siteId, $pageId);
                           
            } else {
                $scriptSuccess = 0;
                $scriptErrors = array($scriptForm->getMessages());   
            }

            $result = array(
                    'success' => $scriptSuccess,
                    'errors' => $scriptErrors
            );

        } else {
            $result = array(
                    'success' => 0,
                    'errors' => array(array('empty' => $translator->translate('tr_meliscms_form_common_errors_Empty datas'))),
            );
        }

        $this->getEventManager()->trigger('meliscms_site_save_script_end', null, $result);        
        return new JsonModel($result);         
    } 

    /**
     * Returns the Site Script Form
     * @param $siteId    
     * @return \Laminas\Form\ElementInterface
     */
    private function getSiteScriptForm($siteId)
    {
        /**
         * Get the config for this form
         */
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered(
            self::PageScriptAppConfigPath,
            'meliscmspagescripteditor_script_form',
            'tool_site_edition_'.$siteId . '_'
        );
   
        /**
         * Generate the form through factory and change ElementManager to
         * have access to our custom Melis Elements
         */
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $scriptForm = $factory->createForm($appConfigForm);
        $scriptForm->setAttribute('action', '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorToolSiteEdition/saveScript');

        return $scriptForm;
    }


    /**
     * Returns the Tool Site Script Exception Form
     * @param $siteId    
     * @return \Laminas\Form\ElementInterface
     */
    private function getSiteScriptExceptionForm($siteId)
    {
        /**
         * Get the config for this form
         */
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered(
            self::PageScriptToolSiteExceptionAppConfigPath,
            'meliscmspagescripteditor_tool_site_exception_form',
            'tool_site_edition_exception_'.$siteId . '_'
        );
   
        /**
         * Generate the form through factory and change ElementManager to
         * have access to our custom Melis Elements
         */
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $scriptExceptionForm = $factory->createForm($appConfigForm);
       
        return $scriptExceptionForm;
    }

    /* Render the script exception table
     * @return \Laminas\View\Model\ViewModel
    */  
    public function renderScriptExceptionsAction()
    {    
        $siteId = (int) $this->params()->fromQuery('siteId', '');
        $view = new ViewModel();

        if ($siteId) {        

            //get the count of the exceptions of the given site
            $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService'); 
            $exceptionCount = $pageScriptEditorService->getScriptExceptions($siteId, null, null)->count();
           
            //get the result table
            $melisTool = $this->getServiceManager()->get('MelisCoreTool');        
            $melisTool->setMelisToolKey('meliscmspagescripteditor', 'meliscmspagescripteditor_site_script_exceptions');//the keys found in app.tools.php    
       
            $tableConfig = $melisTool->getTableConfig();
            $tableId =  $siteId.'MelisCmsPageScriptEditorScriptExceptionsTable';
            $view->siteId = $siteId;                
            $tableConfig['attributes']['id'] =  $tableId;
            $view->tableConfig = $tableConfig;  
            $view->exceptionCount =  $exceptionCount;           
        }
        
        return $view;    
    }


    /**
     * Retrieves the list of pages that excludes the site's scripts
     * @return \Laminas\View\Model\JsonModel
     */
    public function getScriptExceptionsAction()
    {       
        $draw = $this->getRequest()->getPost('draw');
        $resultList = [];

        if ($this->getRequest()->isPost()) {                 
            $postValues = $this->getRequest()->getPost()->toArray();            
            $siteId = (int) $postValues['siteId'];
            $draw = $postValues['draw'];
            $sortCol = null;
            $sortOrder = null;     

            //check if sorting param not empty
            if ($postValues['order']) {                
                $melisTool = $this->getServiceManager()->get('MelisCoreTool');
                $melisTool->setMelisToolKey('meliscmspagescripteditor', 'meliscmspagescripteditor_site_script_exceptions');//the keys found in app.tools.php 

                //get columns of the table to set the order field key
                $order = $postValues['order'];
                $colId = array_keys($melisTool->getColumns());
                $sortCol = $colId[$order[0]['column']];
                $sortOrder = $order[0]['dir'];
            }  

            //get the script exceptions
            if (!empty($siteId)) {                
                //get the melis cms page script editor service
                $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService'); 
                $results = $pageScriptEditorService->getScriptExceptions($siteId, $sortCol, $sortOrder)->toArray();
                               
                if ($results) {
                    $resultList = $results;                   
                } 
            }  
        }

        return new JsonModel([  
            'draw' => (int) $draw,         
            'data' => $resultList,
            'recordsTotal' => count($resultList),
            'recordsFiltered' => count($resultList),
        ]);
    }


    /* Renders the view page in front button
     * @return \Laminas\View\Model\ViewModel
    */
    public function renderTableActionDeleteExceptionAction()
    {
        return new ViewModel();
    }


    /* Saves the site script exception to DB
     * @return \Laminas\View\Model\JsonModel
    */
    public function saveSiteScriptExceptionAction()
    {      
        $errors = [];
        $success = 0;
        $textMessage = "";
        $translator = $this->getServiceManager()->get('translator');
        $textTitle = $translator->translate('tr_meliscmspagescripteditor_tool_site_exception_title');
                
        // Check if post
        $request = $this->getRequest();

        if ($request->isPost()) {          
            $postValues = $request->getPost()->toArray(); 

            $siteId = $postValues['siteId'];
            $pageId = $postValues['tool_site_mcse_page_id'];   
            $textMessage = $postValues['operation'] == 'add' ? $translator->translate('tr_meliscmspagescripteditor_add_exception_error') : $translator->translate('tr_meliscmspagescripteditor_delete_exception_error');
            
            //process here the exception form
            $exceptionForm = $this->getSiteScriptExceptionForm($siteId);
            $exceptionForm->setData($postValues);

            if ($exceptionForm->isValid()) {
                $siteScriptExceptionData = $exceptionForm->getData();
                $scriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
                $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService'); 

                if ($siteScriptExceptionData['tool_site_mcse_page_id']) {
                    
                    if ($postValues['operation'] == 'add') {

                        //check here if the selected page belongs to current site by getting the site id of the page
                        $pageSiteId = $pageScriptEditorService->getSiteId($pageId);

                        //page is ok if its site id is the same with the selected site id from the site tool
                        if ($siteId == $pageSiteId) {

                            //check if already existing in DB
                            $isExisting = $scriptExceptionTable->getEntryByField('mcse_page_id', $pageId)->current();
                 
                            if ($isExisting) {                                   
                                $exceptionForm->get('tool_site_mcse_page_id')->setMessages([
                                    'Duplicate' => $translator->translate('tr_meliscmspagescripteditor_add_exception_duplicate_error')
                                ]);        
                                $errors = $exceptionForm->getMessages(); 
                            } else {
                                //add to exception list if not yet existing    
                                $res = $pageScriptEditorService->addScriptException($siteId, $pageId);

                                if ($res) {
                                    $success = 1;    
                                } 
                            }
                           
                        } else {
                            $exceptionForm->get('tool_site_mcse_page_id')->setMessages([
                                'Wrong Site' => $translator->translate('tr_meliscmspagescripteditor_add_exception_wrong_site_error')
                            ]);        
                            $errors = $exceptionForm->getMessages(); 
                        } 

                    } else {
                        //delete page from the exception list                      
                        $res = $scriptExceptionTable->deleteByField('mcse_page_id', $pageId);
                        
                        if ($res) {
                            $success = 1;    
                        } 
                    }

                    if ($success) {
                        //set success message
                        $textMessage = $postValues['operation'] == 'add' ? $translator->translate('tr_meliscmspagescripteditor_add_exception_success') : $translator->translate('tr_meliscmspagescripteditor_delete_exception_success');
                    } else {
                       
                        //set error message
                        $textMessage = $postValues['operation'] == 'add' ? $translator->translate('tr_meliscmspagescripteditor_add_exception_error') : $translator->translate('tr_meliscmspagescripteditor_delete_exception_error');                                            
                    }  
                }            

            } else {                       
                $errors = $exceptionForm->getMessages();  
            } 


            if ($errors) {
                /**
                 * Get the config for this form
                 */
                $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

                $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered(
                    self::PageScriptToolSiteExceptionAppConfigPath,
                    'meliscmspagescripteditor_tool_site_exception_form',
                    'tool_site_edition_exception_'.$siteId . '_');

                foreach ($errors as $keyError => $valueError) {
                    foreach ($appConfigForm['elements'] as $keyForm => $valueForm) {
                        if ($valueForm['spec']['name'] == $keyError &&
                            !empty($valueForm['spec']['options']['label']))
                            $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                    }
                }
            }            
    
            $result = array(
                    'success' => $success,
                    'errors' => $errors,
                    'textMessage' => $textMessage,
                    'textTitle' => $textTitle
            );
           
        } else {
            $result = array(
                    'success' => $success,
                    'errors' => array(array('empty' => $translator->translate('tr_meliscms_form_common_errors_Empty datas'))),
                    'textMessage' => $textMessage,
                    'textTitle' => $textTitle
            );
        }
          
        return new JsonModel($result);         
    } 
}