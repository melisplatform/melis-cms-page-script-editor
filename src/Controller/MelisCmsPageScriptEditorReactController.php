<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2026 Melis Technology (http://www.melistechnology.com)
 */
namespace MelisCmsPageScriptEditor\Controller;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use MelisCore\Controller\MelisAbstractActionController;

/**
 * JSON API for the native React "Scripts" tab of the CMS Site editor (MelisCms brick).
 * Owned entirely by this module (NOT melis-react-api). Reuses MelisCmsPageScriptEditorService
 * for all business logic (save/exception rules), mirroring the legacy ToolSiteEdition controller.
 *
 * The page-level Scripts tab keeps using the legacy iframe (meliscms_page tool) — only the
 * SITE editor was rebuilt natively, so this API is site-scoped.
 *
 * Routes (generic `/melis/MelisCmsPageScriptEditor/:controller/:action` route already
 * declared in this module's module.config.php):
 *   GET  /melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorReact/site-script?siteId=
 *   POST /melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorReact/save-site-script
 *   GET  /melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorReact/exceptions?siteId=
 *   POST /melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorReact/add-exception
 *   POST /melis/MelisCmsPageScriptEditor/MelisCmsPageScriptEditorReact/delete-exception
 *
 * The page picker (add-exception) reuses the legacy CMS page tree
 * (GET /melis/MelisCms/TreeSites/get-tree-pages-by-page-id) on the React side — no page-listing
 * endpoint here; the "page belongs to this site" rule is enforced by getSiteId() in add-exception.
 */
class MelisCmsPageScriptEditorReactController extends MelisAbstractActionController
{
    /** Rights guard: this tab lives inside the CMS Site tool → gate on the Site tool's access. */
    private const MELIS_KEY = 'meliscms_tool_sites';

    // ─── GET /site-script?siteId= ───────────────────────────────────────────

    public function siteScriptAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $siteId = (int) $this->params()->fromQuery('siteId', 0);
            if (!$siteId) {
                return $this->jsonResponse(['success' => false, 'error' => 'siteId is required'], 400);
            }

            // Réutilise le service (comme le legacy) : getScriptsPerSite + getScriptExceptions.
            $service = $this->getServiceManager()->get('MelisCmsPageScriptEditorService');
            $script  = $service->getScriptsPerSite($siteId)->current();
            // même comptage que le legacy (ToolSiteEdition::renderScriptExceptionsAction) : via le
            // service, qui ne compte que les exceptions dont la page existe encore.
            $exceptionCount = $service->getScriptExceptions($siteId, null, null)->count();

            return $this->jsonResponse([
                'success' => true,
                'data'    => [
                    'siteId'         => $siteId,
                    'script'         => $this->formatScript($script),
                    'exceptionCount' => (int) $exceptionCount,
                ],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── POST /save-site-script ─────────────────────────────────────────────

    public function saveSiteScriptAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $body   = json_decode($this->getRequest()->getContent(), true) ?? [];
            $siteId = (int) ($body['siteId'] ?? 0);
            if (!$siteId) {
                return $this->jsonResponse(['success' => false, 'error' => 'siteId is required'], 400);
            }

            $service = $this->getServiceManager()->get('MelisCmsPageScriptEditorService');
            $mcsId   = isset($body['id']) && $body['id'] ? (int) $body['id'] : null;

            $headTop    = (string) ($body['headTop'] ?? '');
            $headBottom = (string) ($body['headBottom'] ?? '');
            $bodyBottom = (string) ($body['bodyBottom'] ?? '');

            // Même sémantique que le legacy (MelisCmsPageScriptEditorAddScriptHelper::addScriptData) :
            // les 3 champs vides = un SUCCÈS qui supprime l'entrée existante. Sans ça, addScript()
            // court-circuite et renvoie null → success:false sans message → notif rouge « HTTP 200 ».
            if ($headTop === '' && $headBottom === '' && $bodyBottom === '') {
                if (!$mcsId) {
                    // pas d'id fourni : on retrouve l'entrée du site comme le fait le formulaire legacy
                    $current = $service->getScriptsPerSite($siteId)->current();
                    $mcsId   = $current ? (int) ((array) $current)['mcs_id'] : null;
                }
                if ($mcsId) {
                    $this->getServiceManager()->get('MelisCmsScriptTable')->deleteById($mcsId);
                }

                return $this->jsonResponse(['success' => true, 'data' => null]);
            }

            $ok = $service->addScript($siteId, null, $headTop, $headBottom, $bodyBottom, $mcsId);

            if (!$ok) {
                return $this->jsonResponse([
                    'success' => false,
                    'error'   => 'tr_meliscmspagescripteditor_save_script_error',
                ], 500);
            }

            return $this->jsonResponse(['success' => true, 'data' => null]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── GET /exceptions?siteId= ─────────────────────────────────────────────

    public function exceptionsAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $siteId = (int) $this->params()->fromQuery('siteId', 0);
            if (!$siteId) {
                return $this->jsonResponse(['success' => false, 'error' => 'siteId is required'], 400);
            }

            $service = $this->getServiceManager()->get('MelisCmsPageScriptEditorService');
            $rows = $service->getScriptExceptions($siteId, null, null)->toArray();

            $items = [];
            foreach ($rows as $r) {
                $items[] = [
                    'id'       => (int) ($r['mcse_id'] ?? 0),
                    'pageId'   => (int) ($r['mcse_page_id'] ?? 0),
                    'pageName' => (string) ($r['page_name'] ?? ''),
                ];
            }

            return $this->jsonResponse(['success' => true, 'data' => ['items' => $items, 'total' => count($items)]]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── POST /add-exception ─────────────────────────────────────────────────

    public function addExceptionAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $body   = json_decode($this->getRequest()->getContent(), true) ?? [];
            $siteId = (int) ($body['siteId'] ?? 0);
            $pageId = (int) ($body['pageId'] ?? 0);
            if (!$siteId || !$pageId) {
                return $this->jsonResponse(['success' => false, 'error' => 'siteId and pageId are required'], 400);
            }

            $service = $this->getServiceManager()->get('MelisCmsPageScriptEditorService');
            $exceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable');

            // same "page belongs to this site" guard as the legacy tool-site controller
            $pageSiteId = (int) $service->getSiteId($pageId);
            if ($pageSiteId !== $siteId) {
                return $this->jsonResponse(['success' => false, 'error' => 'tr_meliscmspagescripteditor_add_exception_wrong_site_error'], 400);
            }

            if ($exceptionTable->getEntryByField('mcse_page_id', $pageId)->current()) {
                return $this->jsonResponse(['success' => false, 'error' => 'tr_meliscmspagescripteditor_add_exception_duplicate_error'], 409);
            }

            $ok = $service->addScriptException($siteId, $pageId);

            return $this->jsonResponse(['success' => (bool) $ok, 'data' => null]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── POST /delete-exception ──────────────────────────────────────────────

    public function deleteExceptionAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }

        try {
            $body = json_decode($this->getRequest()->getContent(), true) ?? [];
            $id   = (int) ($body['id'] ?? 0);
            if (!$id) {
                return $this->jsonResponse(['success' => false, 'error' => 'id is required'], 400);
            }

            $exceptionTable = $this->getServiceManager()->get('MelisCmsScriptExceptionTable');
            $exceptionTable->deleteById($id);

            return $this->jsonResponse(['success' => true, 'data' => null]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function formatScript($row): ?array
    {
        if (!$row) {
            return null;
        }
        $r = (array) $row;
        return [
            'id'         => (int) ($r['mcs_id'] ?? 0),
            'headTop'    => (string) ($r['mcs_head_top'] ?? ''),
            'headBottom' => (string) ($r['mcs_head_bottom'] ?? ''),
            'bodyBottom' => (string) ($r['mcs_body_bottom'] ?? ''),
        ];
    }

    private function isAuthenticated(): bool
    {
        return $this->getServiceManager()->get('MelisCoreAuth')->hasIdentity();
    }

    private function denyUnlessAccess(): ?HttpResponse
    {
        if (!$this->isAuthenticated()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
        try {
            if (!$this->getServiceManager()->get('MelisCoreRights')->canAccess(self::MELIS_KEY)) {
                return $this->jsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
            }
        } catch (\Throwable) {}
        return null;
    }

    private function jsonResponse(array $data, int $status = 200): HttpResponse
    {
        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $response->setStatusCode($status);
        $response->getHeaders()->addHeaders([
            'Content-Type'           => 'application/json; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response;
    }

    private function errorResponse(\Throwable $e, int $status = 500): HttpResponse
    {
        return $this->jsonResponse([
            'success' => false,
            'error'   => $e->getMessage(),
            'file'    => basename($e->getFile()) . ':' . $e->getLine(),
        ], $status);
    }
}
