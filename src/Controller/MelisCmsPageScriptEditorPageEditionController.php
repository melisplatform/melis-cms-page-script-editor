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

class MelisCmsPageScriptEditorPageEditionController extends MelisAbstractActionController
{
    // The form is loaded from the app.tools array
    const PageScriptAppConfigPath = '/meliscmspagescripteditor/forms/meliscmspagescripteditor_script_form';
    const PageScriptExceptionAppConfigPath = '/meliscmspagescripteditor/forms/meliscmspagescripteditor_script_exception_form';
 
    /* Renders link check tab in Page Edition module
     * @return \Laminas\View\Model\ViewModel
    */    
    public function renderPageScriptEditorAction()
    {        
        $idPage = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));
        $view = new ViewModel();
        $view->idPage = $idPage;        
        return $view;
    }

    /* Renders link check launch form
     * @return \Laminas\View\Model\ViewModel
    */  
    public function renderPageScriptEditorLaunchFormAction()
    {
        $idPage = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));
        $mcse_exclude_site_scripts = 0;

        //get here the script of the given page
        $scriptTable = $this->getServiceManager()->get('MelisCmsScriptTable'); 
        $pageScript = $scriptTable->getEntryByField('mcs_page_id', $idPage)->current();
        $scriptForm = $this->getPageScriptForm($idPage);
        
        //if there's data found, set it to form
        if ($pageScript) {
            $scriptForm->bind($pageScript);
        }        

        //get here the page's site script exception 
        $scriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
        $pageSiteScriptException = $scriptExceptionTable->getEntryByField('mcse_page_id', $idPage)->current();
        $exceptionForm = $this->getPageScriptExceptionForm($idPage);        
        
        //if there's data found, set it to form
        if ($pageSiteScriptException) {
            $exceptionForm->bind($pageSiteScriptException);
            $mcse_exclude_site_scripts = 1;          
        }
       
        $view = new ViewModel();
        $view->idPage = $idPage;        
        $view->scriptForm = $scriptForm;
        $view->exceptionForm = $exceptionForm;
        $view->mcse_exclude_site_scripts = $mcse_exclude_site_scripts;
        return $view;
    }
   
    /* Saves the script to DB
     * @return \Laminas\View\Model\ViewModel
    */
    public function saveScriptAction()
    {   
        $idPage = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));       

        $eventDatas = array('idPage' => $idPage);
        $this->getEventManager()->trigger('meliscms_page_save_script_start', null, $eventDatas);
        $scriptSuccess = 1;
        $exceptionSuccess = 1;
        $scriptErrors = [];
        $exceptionErrors = [];
        $translator = $this->getServiceManager()->get('translator');
                
        // Check if post
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Get values posted and set them in form
            $postValues = $request->getPost()->toArray();         
            $scriptForm = $this->getPageScriptForm($idPage);
            $scriptForm->setData($postValues);

            // Validate the form
            if ($scriptForm->isValid()) {               
                $scriptData = $scriptForm->getData();

                //use helper to add the scripts to DB
                $viewHelperManager = $this->getServiceManager()->get('ViewHelperManager');
                $addScriptHelper = $viewHelperManager->get('melisCmsPageScriptEditorAddScript'); 

                //set site ID to null
                $siteId = null;               
                $scriptSuccess = $addScriptHelper->addScriptData($this->getServiceManager(), $scriptData, $siteId, $idPage);     

            } else {
                $scriptSuccess = 0;
                $scriptErrors = array($scriptForm->getMessages());   
            }

            //process here the exception form
            $exceptionForm = $this->getPageScriptExceptionForm($idPage);
            $exceptionForm->setData($postValues);

            if ($exceptionForm->isValid()) {
                $siteScriptExceptionData = $exceptionForm->getData();

                //insert site's script exception to DB for the given page
                if ($siteScriptExceptionData['mcse_exclude_site_scripts']) {

                    //get the site id given the page template id
                    $pageTplId = $postValues['page_tpl_id'];
                    $tplTable = $this->getServiceManager()->get('MelisEngineTableTemplate');
                    $template = $tplTable->getEntryById($pageTplId)->current();
                    $siteId = $template->tpl_site_id;                   

                    //get the melis cms page script editor service
                    $pageScriptEditorService = $this->getServiceManager()->get('MelisCmsPageScriptEditorService'); 
                    $res = $pageScriptEditorService->addScriptException($siteId, $idPage);

                    if (!$res) {
                        $exceptionSuccess = 0;
                    }

                } else {

                    //if unchecked, remove its exception entry from the DB if there are any  
                    if (!empty($siteScriptExceptionData['mcse_id'])) {
                        $scriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
                        $scriptExceptionTable->deleteById($siteScriptExceptionData['mcse_id']);
                    }     
                }              

            } else {
                $exceptionSuccess = 0;
                $exceptionErrors = array($exceptionForm->getMessages());  
            }

            $errors = ArrayUtils::merge($scriptErrors, $exceptionErrors);     

            if ($scriptSuccess == 1 && $exceptionSuccess == 1) {
                $success = 1;
            } else {
                $success = 0;
            }

            $result = array(
                    'success' => $success,
                    'errors' => $errors
            );


        } else {
            $result = array(
                    'success' => 0,
                    'errors' => array(array('empty' => $translator->translate('tr_meliscms_form_common_errors_Empty datas'))),
            );
        }

        $this->getEventManager()->trigger('meliscms_page_save_script_end', null, $result);        
        return new JsonModel($result);
    } 

    /**
     * Returns the Page Script Form
     * @param $idPage    
     * @return \Laminas\Form\ElementInterface
     */
    private function getPageScriptForm($idPage)
    {
        /**
         * Get the config for this form
         */
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered(
            self::PageScriptAppConfigPath,
            'meliscmspagescripteditor_script_form',
            'page_edition_'.$idPage . '_'
        );
   
        /**
         * Generate the form through factory and change ElementManager to
         * have access to our custom Melis Elements
         */
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $scriptForm = $factory->createForm($appConfigForm);
        $scriptForm->setAttribute('action', '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorPageEdition/saveScript');

        return $scriptForm;
    }


    /**
     * Returns the Page Script Exception Form
     * @param $idPage
     * @return \Laminas\Form\ElementInterface
     */
    private function getPageScriptExceptionForm($idPage)
    {
        /**
         * Get the config for this form
         */
        $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');

        $appConfigForm = $melisMelisCoreConfig->getFormMergedAndOrdered(
            self::PageScriptExceptionAppConfigPath,
            'meliscmspagescripteditor_script_exception_form',
            'page_edition_'.$idPage . '_'
        );
   
        /**
         * Generate the form through factory and change ElementManager to
         * have access to our custom Melis Elements
         */
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $exceptionForm = $factory->createForm($appConfigForm);
        $exceptionForm->setAttribute('action', '/melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorPageEdition/saveScript');

        return $exceptionForm;
    }
}