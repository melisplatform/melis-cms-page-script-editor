<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageScriptEditor\Service;

use Laminas\Http\Request;

/**
 * 
 * This service executes the testing of page links
 *
 */
class MelisCmsPageScriptEditor extends MelisGeneralService
{
                
    
    /**
     * This method will save the script content for the site or page
     * @param int $siteId
     * @param int $pageId
     * @param String $headTopScript
     * @param String $headBottomScript
     * @param String $bodyBottomScript
     * @param int $mcs_id, the primary key of the mcs_script table
     * @return null if the saving is failed
     */
    public function addScript($siteId = null, $pageId = null, $headTopScript = null, $headBottomScript = null, $bodyBottomScript = null, $mcs_id = null)
    { 
        $arrayParameters = $this->makeArrayFromParameters(__METHOD__, func_get_args());
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_save_script_start', $arrayParameters);
        $res = null;

        if (!empty($arrayParameters['headTopScript'])  || !empty($arrayParameters['headBottomScript']) || !empty($arrayParameters['bodyBottomScript'])) {
            
            //get the current user id          
            $userId = null;
            $melisCoreAuth = $this->getServiceManager()->get('MelisCoreAuth');
            $userAuthDatas =  $melisCoreAuth->getStorage()->read();
            if (!empty($userAuthDatas->usr_id)) {
                $userId = (int) $userAuthDatas->usr_id;
            }
            
            $scriptData = array(
                        'mcs_site_id' => $arrayParameters['siteId'],
                        'mcs_page_id' => $arrayParameters['pageId'],
                        'mcs_head_top' => $arrayParameters['headTopScript'],
                        'mcs_head_bottom' => $arrayParameters['headBottomScript'],
                        'mcs_body_bottom' => $arrayParameters['bodyBottomScript'],
                        'mcs_date_edition' => date('Y-m-d H:i:s'),
                        'mcs_user_id' => $userId
                    );
                  
            $melisCmsScriptTable = $this->getServiceManager()->get('MelisCmsScriptTable');           
            $res = $melisCmsScriptTable->save($scriptData, $arrayParameters['mcs_id']);           
        }

        $arrayParameters['result'] = $res;
        $arrayParameters = $this->sendEvent('meliscmspagescripteditor_save_script_end', $arrayParameters);
        return $arrayParameters['result'];
    }
  
}