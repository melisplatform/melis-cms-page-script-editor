<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */
namespace MelisCmsPageScriptEditor\Service;
use MelisCore\Service\MelisGeneralService;

/**
 * 
 * This service executes the testing of page links
 *
 */
class MelisCmsPageScriptEditorService extends MelisGeneralService
{                    
    /**
     * This method will save the script content for the site or page
     * @param int|null $siteId
     * @param int|null $pageId
     * @param String $headTopScript
     * @param String $headBottomScript
     * @param String $bodyBottomScript
     * @param int $mcs_id, the primary key of the mcs_script table
     * @return null if the saving failed
     */
    public function addScript($siteId = null, $pageId = null, $headTopScript = null, $headBottomScript = null, $bodyBottomScript = null, $mcs_id = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_save_script_start', $arrayParameters);
        $res = null;

        if (!empty($arrayParameters['headTopScript'])  || !empty($arrayParameters['headBottomScript']) || !empty($arrayParameters['bodyBottomScript'])) {
                   
            $scriptData = array(
                        'mcs_site_id' => $arrayParameters['siteId'],
                        'mcs_page_id' => $arrayParameters['pageId'],
                        'mcs_head_top' => $arrayParameters['headTopScript'],
                        'mcs_head_bottom' => $arrayParameters['headBottomScript'],
                        'mcs_body_bottom' => $arrayParameters['bodyBottomScript'],
                        'mcs_date_edition' => date('Y-m-d H:i:s'),
                        'mcs_user_id' => $this->getCurrentUserId()
                    );
                  
            $melisCmsScriptTable = $this->getServiceManager()->get('MelisCmsScriptTable');           
            $res = $melisCmsScriptTable->save($scriptData, $arrayParameters['mcs_id']);                    
        }

        $arrayParameters['result'] = $res;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_save_script_end', $arrayParameters);
        return $arrayParameters['result'];
    }

    /**
     * This method will save the page as an exception in using the site's scripts
     * @param int|null $siteId
     * @param int|null $pageId   
     * @return array
     */
    public function addScriptException($siteId = null, $pageId = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_save_script_start', $arrayParameters);
        
        //check first if there's no entry yet in DB
        $melisCmsScriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
        $exception = $melisCmsScriptExceptionTable->getEntryByField('mcse_page_id', $pageId)->current();
        $res = 1;

        //add exception if not yet existing
        if (empty($exception)) {
            $exceptionData = array(
                    'mcse_site_id' => $arrayParameters['siteId'],
                    'mcse_page_id' => $arrayParameters['pageId'],                      
                    'mcse_date_creation' => date('Y-m-d H:i:s'),
                    'mcse_user_id' => $this->getCurrentUserId()
                );              
                  
            $res = $melisCmsScriptExceptionTable->save($exceptionData);   
        }
            
        $arrayParameters['result'] = $res;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_save_script_end', $arrayParameters);
        return $arrayParameters['result'];
    }

     /**
     * This will return the current user id that is using the platform  
     * @return int
     */
    private function getCurrentUserId()
    {
        //get the current user id          
        $userId = null;
        $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
        $userAuthDatas =  $melisCoreAuth->getStorage()->read();
        if (!empty($userAuthDatas->usr_id)) {
            $userId = (int) $userAuthDatas->usr_id;
        }

        return $userId;
    }

     /**
     * This method will retrieve the list of pages that exclude the site scripts of the given site id 
     * @param int|null $siteId
     * @param string|null $sortColumn
     * @param string|null $sortOrder
     * @return array
     */
    public function getScriptExceptions($siteId = null, $sortColumn = null, $sortOrder = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_script_exception_start', $arrayParameters);
        
        $melisCmsScriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
        $exceptions = $melisCmsScriptExceptionTable->getScriptExceptions($arrayParameters['siteId'], $arrayParameters['sortColumn'], $arrayParameters['sortOrder']);
        
        $arrayParameters['result'] = $exceptions;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_script_exception_end', $arrayParameters);
        return $arrayParameters['result'];
    }

    /**
     * This method will retrieve the scripts for the given site id
     * @param int|null $siteId
     * @return array
     */
    public function getScriptsPerSite($siteId = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_site_script_start', $arrayParameters);
        
        $melisCmsScriptTable = $this->getServiceManager()->get('MelisCmsScriptTable'); 
        $siteScripts = $melisCmsScriptTable->getEntryByField('mcs_site_id', $arrayParameters['siteId']);
        
        $arrayParameters['result'] = $siteScripts;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_site_script_end', $arrayParameters);
        return $arrayParameters['result'];
    }

    /**
     * This method will retrieve the scripts for the given page id
     * @param int|null $pageId
     * @return array
     */
    public function getScriptsPerPage($pageId = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_page_script_start', $arrayParameters);
        
        $melisCmsScriptTable = $this->getServiceManager()->get('MelisCmsScriptTable'); 
        $pageScripts = $melisCmsScriptTable->getEntryByField('mcs_page_id', $arrayParameters['pageId']);
        
        $arrayParameters['result'] = $pageScripts;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_page_script_end', $arrayParameters);
        return $arrayParameters['result'];
    }


    /**
     * This method will retrieve the combination of site and page scripts for the given page id
     * This will also consider whether the page excludes the site's scripts or not
     * @param int|null $pageId
     * @return array
     */
    public function getMixedScriptsPerPage($pageId = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_mixed_scripts_per_page_start', $arrayParameters);
        $headTopScript = "";
        $headBottomScript = "";
        $bodyBottomScript = "";
        $finalScripts = [];

        $scriptExceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable'); 
        $exception = $scriptExceptionTable->getEntryByField('mcse_page_id', $arrayParameters['pageId'])->current();

        //if the given page is in the exception list, get only the page scripts
        if ($exception) {
  
            $pageScripts = current($this->getScriptsPerPage($pageId)->toArray());

            //set the head top script           
            $headTopScript = !empty($pageScripts['mcs_head_top']) ? $pageScripts['mcs_head_top'] . "\r\n" : '';
       
            //set the head bottom script          
            $headBottomScript = !empty($pageScripts['mcs_head_bottom']) ? $pageScripts['mcs_head_bottom'] . "\r\n" : '';
         
            //set the body bottom script           
            $bodyBottomScript = !empty($pageScripts['mcs_body_bottom']) ? $pageScripts['mcs_body_bottom'] . "\r\n" : '';      

        } else {
            //get the site id of the page
            $siteId = $this->getSiteId($arrayParameters['pageId']);

            $siteScripts = current($this->getScriptsPerSite($siteId)->toArray());
            $pageScripts = current($this->getScriptsPerPage($pageId)->toArray());

            //set the head top script
            $headTopScript = !empty($siteScripts['mcs_head_top']) ? ($siteScripts['mcs_head_top']."\r\n") : '';
            $headTopScript = $headTopScript . (!empty($pageScripts['mcs_head_top']) ? $pageScripts['mcs_head_top'] : '');
       
            //set the head bottom script
            $headBottomScript = !empty($siteScripts['mcs_head_bottom']) ? ($siteScripts['mcs_head_bottom']."\r\n") : '';
            $headBottomScript = $headBottomScript . (!empty($pageScripts['mcs_head_bottom']) ? $pageScripts['mcs_head_bottom'] : '');
         
            //set the body bottom script
            $bodyBottomScript = !empty($siteScripts['mcs_body_bottom']) ? ($siteScripts['mcs_body_bottom'] . "\r\n") : '';
            $bodyBottomScript = $bodyBottomScript . (!empty($pageScripts['mcs_body_bottom']) ? $pageScripts['mcs_body_bottom'] : '');
        }      

        $finalScripts = array (
                'headTopScript' => $headTopScript,
                'headBottomScript' => $headBottomScript,
                'bodyBottomScript' => $bodyBottomScript
            );
        
        $arrayParameters['result'] = $finalScripts;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_mixed_scripts_per_page_end', $arrayParameters);
        return $arrayParameters['result'];
    }


    /**
     * Updates the scripts of the given page
     * 
     * @param int $idPage Id of page asked
     * @param string $contentGenerated Content to be changed
     * @return string
     */
    public function updatePageScripts($idPage, $contentGenerated)
    {
        $newContent = $contentGenerated;

        //get the mixed scripts of the page
        $scripts = $this->getMixedScriptsPerPage($idPage);               
                
        if (!empty($scripts['headTopScript'])) {
            $headRegex = '/(<head\s*>)/';
            $newContent = preg_replace($headRegex, "$1\r\n".$scripts['headTopScript']."\r\n", $newContent, 1);
        }

        if (!empty($scripts['headBottomScript'])) {
            $headRegex = '/(<\/head\s*>)/';                  
            $newContent = preg_replace($headRegex, "\r\n".$scripts['headBottomScript']."\r\n$1", $newContent, 1);                   
        }

        if (!empty($scripts['bodyBottomScript'])) {
            $bodyRegex = '/(<\/body\s*>)/';
            $newContent = preg_replace($bodyRegex, "\r\n".$scripts['bodyBottomScript']."\r\n$1", $newContent, 1);
        }                        
          
        return $newContent;
    }
  
     /**
     * This will retrieve the site ID of the page whether saved or published
     * 
     * @param int $idPage Id of page asked     
     * @return array
     */
    public function getSiteId($pageId = null)
    {
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_site_id_start', $arrayParameters);

        //check first the published pages
        $melisPage = $this->getServiceManager()->get('MelisEnginePage');
        $datasPage = $melisPage->getDatasPage($arrayParameters['pageId'], 'published'); 
        $pageSiteId = 0;

        if ($datasPage->getMelisTemplate()) {
            $pageSiteId = $datasPage->getMelisTemplate()->tpl_site_id;
        }            

        //if page not yet published, check the saved pages
        if (empty($pageSiteId)) {                
            $datasPage = $melisPage->getDatasPage($pageId,'saved'); 

            if ($datasPage->getMelisTemplate()) {
                $pageSiteId = $datasPage->getMelisTemplate()->tpl_site_id;
            }                
        }

        $arrayParameters['result'] = $pageSiteId;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_get_site_id_end', $arrayParameters);
        return $arrayParameters['result'];
    }
}