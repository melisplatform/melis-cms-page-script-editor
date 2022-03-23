<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageScriptEditor\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use MelisCore\Listener\MelisGeneralListener;


/**
 * This listener will copy the defined scripts and the exception configuration of the copied page to the duplicate page
 * 
 */
class MelisCmsPageScriptEditorDuplicatePageListener extends MelisGeneralListener implements ListenerAggregateInterface
{

	public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
        	'*',
        	[
        		'meliscms_page_duplicate_start', 
        		'melis_cms_duplicate_page_start'
        	], 
        	function ($event) {

        		$params = $event->getParams();
        		$pageId = $params['pageId'];

        		$sm = $event->getTarget()->getServiceManager();				
				$container = new Container('meliscore');

				//get the page script data of the given page and save it to session 
				$melisCmsPageScriptEditorService = $sm->get('MelisCmsPageScriptEditorService');
				$pageScriptData = $melisCmsPageScriptEditorService->getScriptsPerPage($pageId)->toArray();
				$pageScriptExceptionData = $melisCmsPageScriptEditorService->getScriptsExceptionPerPage($params['pageId'])->toArray();
				
				$container['melis-cms-page-script-editor-script-data'] = $pageScriptData;
				$container['melis-cms-page-script-editor-script-exception-data'] = $pageScriptExceptionData;			
   			
        	},
        100
        );


        $this->attachEventListener(
            $events,
        	'*',
        	[
        		'meliscms_page_duplicate_end', 
        		'melis_cms_duplicate_page_end'
        	], 
        	function ($event) {       
        		$params = $event->getParams();
        		$pageId = $params['pageId'];

        		//for the duplicate tree action
        		if ($event->getName() == 'melis_cms_duplicate_page_end') {
        			$pageId = $params['results'];
        		}

        		$sm = $event->getTarget()->getServiceManager();		
        		$container = new Container('meliscore');
        		$scriptData = current($container['melis-cms-page-script-editor-script-data']);
        		$scriptExceptionData = current($container['melis-cms-page-script-editor-script-exception-data']);

				//add here the script and script exception entries if there are any
			    $melisCmsPageScriptEditorService = $sm->get('MelisCmsPageScriptEditorService');

			    if ($scriptData) {
			    	$addScript = $melisCmsPageScriptEditorService->addScript($scriptData['mcs_site_id'], $pageId, $scriptData['mcs_head_top'], $scriptData['mcs_head_bottom'], $scriptData['mcs_body_bottom']);
			    }			    

			    if ($scriptExceptionData) {
			    	$addScriptException = $melisCmsPageScriptEditorService->addScriptException($scriptExceptionData['mcse_site_id'], $pageId);
			    }

			    //unset session data
			    unset($container['melis-cms-page-script-editor-script-data']);
			    unset($container['melis-cms-page-script-editor-script-exception-data']);
        	},
        100
        );
    }
}