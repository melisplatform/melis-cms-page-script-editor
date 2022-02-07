<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2015 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageScriptEditor\Listener;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use MelisCore\Listener\MelisGeneralListener;

/**
 * This listener will update the scripts for the given page
 * 
 */
class MelisCmsPageScriptEditorSavePageListener extends MelisGeneralListener implements ListenerAggregateInterface
{
	
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $identifier = 'MelisCms';

        $eventsName = [
            'meliscms_page_save_start',
            'meliscms_page_publish_start',
        ];

        $priority = 100;

        $this->attachEventListener($events, $identifier, $eventsName, [$this, 'savePageScript'], $priority);
    }

    public function savePageScript(EventInterface $event)
    {
        $sm = $event->getTarget()->getEvent()->getApplication()->getServiceManager();
        $melisCoreDispatchService = $sm->get('MelisCoreDispatch');    

        // Save script tab
        list($success, $errors, $datas) = $melisCoreDispatchService->dispatchPluginAction(
            $event,
            'meliscms',
            'action-page-tmp',
            'MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorPageEdition',
            array_merge(['action' => 'saveScript'], [])
        );

        if (!$success)
            return;             
    }
}