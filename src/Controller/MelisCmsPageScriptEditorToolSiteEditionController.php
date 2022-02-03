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
use Laminas\Stdlib\ArrayUtils;

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

        //get the pages that exclude the site's scripts and set it to hidden field
        $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService'); 
        $pagesException = $pageScriptEditorService->getScriptExceptions($siteId, null, null)->toArray();

        if ($pagesException) {
            $pages = [];
           
            foreach ($pagesException as $key => $page) {
                $pages[] = $page['mcse_page_id'];
            }

            $pages = implode(',',$pages); 
            $element = $scriptExceptionForm->get('tool_site_mcse_page_id');                
            $element->setValue($pages);                                    
        }

        $view = new ViewModel();
        $view->siteId = $siteId;
        $view->scriptForm = $scriptForm;     
        $view->scriptExceptionForm = $scriptExceptionForm;       
        return $view;
    }
    
    /* Saves the script to DB
     * @return \Laminas\View\Model\ViewModel
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
                
                //if there's at least one script value, proceed with the saving
                if (!empty($scriptData['mcs_head_top']) || !empty($scriptData['mcs_head_bottom']) || !empty($scriptData['mcs_body_bottom'])) {

                    //set page ID to null
                    $idPage = null;

                    //get the melis cms page script editor service
                    $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService');   
                    $res = $pageScriptEditorService->addScript($siteId, $idPage , $scriptData['mcs_head_top'], $scriptData['mcs_head_bottom'], $scriptData['mcs_body_bottom'], $scriptData['mcs_id']);

                    if (!$res) {
                        $scriptSuccess = 0;
                    }

                } else {
                    if (!empty($scriptData['mcs_id'])) {
                        // All fields are empty, let's delete the entry
                        $scriptTable = $this->getServiceManager()->get('MelisCmsScriptTable'); 
                        $scriptTable->deleteById($scriptData['mcs_id']);
                    }                   
                }               

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
     * Returns the Page Script Form
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
            //get the result table
            $melisTool = $this->getServiceManager()->get('MelisCoreTool');        
            $melisTool->setMelisToolKey('meliscmspagescripteditor', 'meliscmspagescripteditor_site_script_exceptions');//the keys found in app.tools.php    
       
            $tableConfig = $melisTool->getTableConfig();
            $tableId =  $siteId.'MelisCmsPageScriptEditorScriptExceptionsTable';
            $view->siteId = $siteId;                
            $tableConfig['attributes']['id'] =  $tableId;
            $view->tableConfig = $tableConfig;              
        }
        
        return $view;    
    }


    /**
     * Retrieves the list of pages that excludes the site's scripts
     * @return \Laminas\View\Model\JsonModel
     */
    public function getScriptExceptionsAction()
    {       
        $resultList = [];

        if ($this->getRequest()->isPost()) {      
            $request = $this->getRequest();
            $postValues = get_object_vars($request->getPost());            
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
            'data' => $resultList           
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
     * @return \Laminas\View\Model\ViewModel
    */
    public function saveSiteScriptExceptionAction()
    {      
        $errors = [];
        $success = 1;
        $textMessage = "";
        $translator = $this->getServiceManager()->get('translator');
        $textTitle = $translator->translate('tr_meliscmspagescripteditor_tool_site_exception_title');
                
        // Check if post
        $request = $this->getRequest();

        if ($request->isPost()) {          
            $postValues = get_object_vars($request->getPost()); 
            $siteId = $postValues['siteId'];
            $pageIdException = $postValues['tool_site_mcse_page_id'];   
            $textMessage = $postValues['operation'] == 'add' ? $translator->translate('tr_meliscmspagescripteditor_add_exception_error') : $translator->translate('tr_meliscmspagescripteditor_delete_exception_error');
            
            //process here the exception form
            $exceptionForm = $this->getSiteScriptExceptionForm($siteId);
            $exceptionForm->setData($postValues);

            if ($exceptionForm->isValid()) {
                $siteScriptExceptionData = $exceptionForm->getData();

                //insert site's script exception to DB for the given page
                if ($siteScriptExceptionData['tool_site_mcse_page_id']) {

                    //delete first the page exception of the site
                    $scriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
                    $scriptExceptionTable->deleteByField('mcse_site_id', $siteId);
                    $res = null;
            
                    //add here the updated list of page exceptions              
                    $explode = explode(',', $siteScriptExceptionData['tool_site_mcse_page_id']);
                    foreach ($explode as $page) {
                        //get the melis cms page script editor service
                        $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService'); 
                        $res = $pageScriptEditorService->addScriptException($siteId, $page);
                     
                        if (!$res) {
                            $success = 0;                                     
                            break;
                        } 
                    }  

                    if ($success) {
                        //set success message
                        $textMessage = $postValues['operation'] == 'add' ? $translator->translate('tr_meliscmspagescripteditor_add_exception_success') : $translator->translate('tr_meliscmspagescripteditor_delete_exception_success');
                    }  
                }            

            } else {
                $success = 0;               
                $errors = array($exceptionForm->getMessages());  
            } 
    
            $result = array(
                    'success' => $success,
                    'errors' => $errors,
                    'textMessage' => $textMessage,
                    'textTitle' => $textTitle
            );
           
        } else {
            $result = array(
                    'success' => 0,
                    'errors' => array(array('empty' => $translator->translate('tr_meliscms_form_common_errors_Empty datas'))),
                    'textMessage' => $textMessage,
                    'textTitle' => $textTitle
            );
        }
          
        return new JsonModel($result);         
    } 
    

    /* This will check if the selected page ID from the tree view belongs to the selected site 
     * @return \Laminas\View\Model\ViewModel
    */
    public function checkPageAction()
    {              
        // Check if post
        $request = $this->getRequest();
        $result = false;

        if ($request->isPost()) {          
            $postValues = get_object_vars($request->getPost()); 
            $pageId = $postValues['pageId'];
            $selectedSiteId = $postValues['siteId'];

            //get the site id of the page, check first the published pages
            $melisPage = $this->getServiceManager()->get('MelisEnginePage');
            $datasPage = $melisPage->getDatasPage($pageId, 'published'); 

            if ($datasPage->getMelisTemplate()) {
                $pageSiteId = $datasPage->getMelisTemplate()->tpl_site_id;
            }            

            //if page not yet published, //check the saved pages
            if (empty($pageSiteId)) {                
                $datasPage = $melisPage->getDatasPage($pageId,'saved'); 

                if ($datasPage->getMelisTemplate()) {
                    $pageSiteId = $datasPage->getMelisTemplate()->tpl_site_id;
                }                
            }

            //page is ok if it's site id is the same with the selected site id
            if ($selectedSiteId == $pageSiteId) {
                $result = array(
                    'pageOk' => 1                     
                );
            } else {
                $translator = $this->getServiceManager()->get('translator');
                $result = array(
                    'pageOk' => 0,
                    'textTitle' => $translator->translate('tr_meliscmspagescripteditor_tool_site_exception_title'),
                    'textMessage' => $translator->translate('tr_meliscmspagescripteditor_add_exception_page_error')                
                );
            }
        }

        return new JsonModel($result);         
    }


}