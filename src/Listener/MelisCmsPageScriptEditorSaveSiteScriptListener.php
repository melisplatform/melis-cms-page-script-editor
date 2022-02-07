<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageScriptEditor\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener will update the scripts of the site
 * 
 */
class MelisCmsPageScriptEditorSaveSiteScriptListener extends MelisGeneralListener implements ListenerAggregateInterface
{	 
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->attachEventListener(
            $events,
        	'MelisCms',
        	'meliscms_site_save_end', 
        	function($event){     
                $sm = $event->getTarget()->getEvent()->getApplication()->getServiceManager();
                $params = $event->getParams();

                //save script tab if all of the previous tabs are processed successfully
                if ($params['success']) {
                    $results = $event->getTarget()->forward()->dispatch(
                                'MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorToolSiteEdition',
                                array_merge(['action' => 'saveSiteScript'], $params))->getVariables(); 
                }                               
        	},
        110
        );
    }
}