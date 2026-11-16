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
use Laminas\Session\Container;
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

        $priority = 99;

        $this->attachEventListener($events, $identifier, $eventsName, [$this, 'savePageScript'], $priority);
    }

    public function savePageScript(EventInterface $event)
    {
        $success = 1;

        // Get the MelisCms Module session as page is saved in it
        $container = new Container('meliscms');

        // Update from the different save actions done
        if (isset($container['action-page-tmp'])) {
            if (isset($container['action-page-tmp']['success']))
                $success = $container['action-page-tmp']['success'];
        }

        if (!$success)
            return;

        $sm = $event->getTarget()->getEvent()->getApplication()->getServiceManager();
        $melisCoreDispatchService = $sm->get('MelisCoreDispatch');

        // The saved page's id, set by MelisCms' save listener (priority 100, before this one): on a page
        // creation the request still says idPage=0 (Mantis #0011371). The dispatch merges every action's
        // datas recursively, so idPage may have become a list of the same id.
        $params = ['action' => 'saveScript'];
        $savedId = $container['action-page-tmp']['datas']['idPage'] ?? null;
        if (is_array($savedId)) {
            $savedId = reset($savedId);
        }
        if ((int) $savedId > 0) {
            $params['idPage'] = (int) $savedId;
        }

        // Save script tab
        list($success, $errors, $datas) = $melisCoreDispatchService->dispatchPluginAction(
            $event,
            'meliscms',
            'action-page-tmp',
            'MelisCmsPageScriptEditor\Controller\MelisCmsPageScriptEditorPageEdition',
            $params
        );

        if (!$success)
            return;
    }
}
