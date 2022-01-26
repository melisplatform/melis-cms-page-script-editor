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

class MelisCmsPageScriptEditorPageEditionController extends MelisAbstractActionController
{
    // The form is loaded from the app.tools array
    const PageScriptAppConfigPath = '/meliscmspagescripteditor/forms/meliscmspagescripteditor_script_form';

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

        //get the page script form
        $melisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig'); 
        $appConfigForm = $melisCoreConfig->getFormMergedAndOrdered('meliscmspagescripteditor/forms/meliscmspagescripteditor_script_form', 'meliscmspagescripteditor_script_form');
       
         // Factoring event and pass to view
        $factory = new \Laminas\Form\Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $form = $factory->createForm($appConfigForm);
      
        $view = new ViewModel();
        $view->idPage = $idPage;        
        $view->scriptForm = $form;
   
        return $view;
    }


    
    /* Saves the script to DB
     * @return \Laminas\View\Model\ViewModel
    */
    public function saveScript(){
        dump('save script function');

        $idPage = $this->params()->fromRoute('idPage', $this->params()->fromQuery('idPage', ''));        
        $eventDatas = array('idPage' => $idPage);
        $this->getEventManager()->trigger('meliscms_page_savesscript_start', null, $eventDatas);
        
        // Get the form properly loaded
        $seoForm = $this->getSeoPageForm($idPage);

        // Check if post
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Get values posted and set them in form
            $postValues = $request->getPost()->toArray();

            $seoForm->setData($postValues);

            // Validate the form
            if ($seoForm->isValid())
            {
                // Get datas validated
                $datas = $seoForm->getData();
                
                $allEmpty = true;
                foreach ($datas as $data)
                {
                    if (!empty($data))
                    {
                        $allEmpty = false;
                        break;
                    }
                }
                
                $success = 1;
                $datas['pseo_id'] = $idPage;
                
                if (substr($datas['pseo_url'], 0, 1) == '/')
                    $datas['pseo_url'] = substr($datas['pseo_url'], 1, strlen($datas['pseo_url']));
                if (substr($datas['pseo_url_redirect'], 0, 1) == '/')
                    $datas['pseo_url_redirect'] = substr($datas['pseo_url_redirect'], 1, strlen($datas['pseo_url_redirect']));
                if (substr($datas['pseo_url_301'], 0, 1) == '/')
                    $datas['pseo_url_301'] = substr($datas['pseo_url_301'], 1, strlen($datas['pseo_url_301']));

                if (!$allEmpty) 
                {
                    
                    
                    // Cleaning special char and white spaces on SEO Url
                    $enginePage = $this->getServiceManager()->get('MelisEngineTree');
                    $datas['pseo_url'] = $enginePage->cleanString(mb_strtolower($datas['pseo_url']));
                    // Checking for spaces
                    if (preg_match('/\s/', $datas['pseo_url']))
                    {
                        $datas['pseo_url'] = str_replace(" ", "", $datas['pseo_url']);
                    }
                    
                    $res = $melisTablePageSeo->save($datas, $idPage);
                }
                else
                {
                    // All field are empty, let's delete the entry
                    $melisTablePageSeo->deleteById($idPage);
                }
    
                $result = array(
                        'success' => $success,
                        'errors' => array(),
                );

            }
            else
            {
                // Add labels of errors
                $errors = $seoForm->getMessages();
                $melisMelisCoreConfig = $this->getServiceManager()->get('MelisCoreConfig');
                $appConfigForm = $melisMelisCoreConfig->getItem(PageSeoController::PageSeoAppConfigPath, $idPage . '_');
                $appConfigForm = $appConfigForm['elements'];
                
                foreach ($errors as $keyError => $valueError)
                {
                    foreach ($appConfigForm as $keyForm => $valueForm)
                    {
                        if ($valueForm['spec']['name'] == $keyError &&
                                !empty($valueForm['spec']['options']['label']))
                                    $errors[$keyError]['label'] = $valueForm['spec']['options']['label'];
                    }
                }
                
                // Get validation errors
                $result = array(
                        'success' => 0,
                        'errors' => array($errors),
                );

            }
        }
        else
        {
            $result = array(
                    'success' => 0,
                    'errors' => array(array('empty' => $translator->translate('tr_meliscms_form_common_errors_Empty datas'))),
            );
        }

        $this->getEventManager()->trigger('meliscms_page_saveseo_end', null, $result);
        
        return new JsonModel($result);

    } 

    /**
     * Returns the Page Property Form
     *
     * @param $idPage
     * @param $isNew
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
            $idPage . '_'
        );

        /** Overriding the Page properties form by calling listener */
        $modifiedForm = $this->getEventManager()->trigger(
            'modify_page_properties_form_config',
            $this,
            ['appConfigForm' => $appConfigForm]
        );

        /** Override appConfigForm with the modified value from the last-touch listener */
        $appConfigForm = empty($modifiedForm->last()) ? $appConfigForm : $modifiedForm->last();

        if ($isNew == false) {
            // Lang not changeable after creation
            $appConfigForm = $melisMelisCoreConfig->setFormFieldDisabled($appConfigForm, 'plang_lang_id', true);
            $appConfigForm = $melisMelisCoreConfig->setFormFieldRequired($appConfigForm, 'plang_lang_id', false);
        }

        /**
         * Generate the form through factory and change ElementManager to
         * have access to our custom Melis Elements
         */

        $factory = new Factory();
        $formElements = $this->getServiceManager()->get('FormElementManager');
        $factory->setFormElementManager($formElements);
        $propertyForm = $factory->createForm($appConfigForm);

        return $propertyForm;
    }
}