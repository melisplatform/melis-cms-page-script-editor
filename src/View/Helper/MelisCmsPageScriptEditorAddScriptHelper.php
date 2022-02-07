<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2019 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageScriptEditor\View\Helper;

use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Helper\AbstractHelper;

/**
 * Add the scripts of the page or site to DB
 *
 */
class MelisCmsPageScriptEditorAddScriptHelper extends AbstractHelper
{
    /*
    * this will add the scripts of the site or page to DB
    */
    public function addScriptData($serviceManager, $scriptData, $siteId = null, $pageId = null)
    {
        $scriptSuccess = 1;

        //if there's at least one script value, proceed with the saving
        if (!empty($scriptData['mcs_head_top']) || !empty($scriptData['mcs_head_bottom']) || !empty($scriptData['mcs_body_bottom'])) {
                          
            //get the melis cms page script editor service
            $pageScriptEditorService = $serviceManager->get('MelisCmsPageScriptEditorService');   
            $res = $pageScriptEditorService->addScript($siteId, $pageId , $scriptData['mcs_head_top'], $scriptData['mcs_head_bottom'], $scriptData['mcs_body_bottom'], $scriptData['mcs_id']);

            if (!$res) {
                $scriptSuccess = 0;
            }

        } else {
            if (!empty($scriptData['mcs_id'])) {
                // All fields are empty, let's delete the entry
                $scriptTable = $serviceManager->get('MelisCmsScriptTable'); 
                $scriptTable->deleteById($scriptData['mcs_id']);
            }                   
        } 

        return $scriptSuccess;
    }

}