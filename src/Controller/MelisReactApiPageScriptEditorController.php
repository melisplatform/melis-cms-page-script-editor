<?php

namespace MelisCmsPageScriptEditor\Controller;

use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use MelisCore\Controller\MelisAbstractActionController;

/**
 * API REST de l'onglet SCRIPTS de l'éditeur de page — MODULAIRE (dans melis-cms-page-script-editor).
 * Lit les scripts custom d'une page (melis_cms_scripts). Route mergée via le Module getConfig.
 *   GET /melis/react-api/cms-page/scripts?idPage=X → { headTop, headBottom, bodyBottom, editDate }
 */
class MelisReactApiPageScriptEditorController extends MelisAbstractActionController
{
    private const MELIS_KEY = 'meliscms_page_script_editor';

    /**
     * These endpoints inject custom <script>/HTML into the PUBLIC front rendering of a page, so they
     * must require the page-script-editor tool right — not merely a session. Previously any
     * authenticated BO user (even with zero CMS rights) could plant persistent XSS on any page.
     */
    private function denyUnlessAccess(): ?HttpResponse
    {
        $sm = $this->getServiceManager();
        if (!$sm->get('MelisCoreAuth')->hasIdentity()) {
            return $this->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
        try {
            if (!$sm->get('MelisCoreRights')->canAccess(self::MELIS_KEY)) {
                return $this->json(['success' => false, 'error' => 'Forbidden'], 403);
            }
        } catch (\Throwable) {}
        return null;
    }

    public function getAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        try {
            $idPage = (int) $this->params()->fromQuery('idPage', 0);
            $db = $this->getServiceManager()->get('Laminas\Db\Adapter\AdapterInterface');
            $rows = iterator_to_array($db->query(
                'SELECT mcs_head_top, mcs_head_bottom, mcs_body_bottom, mcs_date_edition
                 FROM melis_cms_scripts WHERE mcs_page_id = ? ORDER BY mcs_id DESC LIMIT 1', [$idPage]));
            $r = $rows[0] ?? [];
            return $this->json(['success' => true, 'data' => [
                'idPage'     => $idPage,
                'headTop'    => $r['mcs_head_top'] ?? '',
                'headBottom' => $r['mcs_head_bottom'] ?? '',
                'bodyBottom' => $r['mcs_body_bottom'] ?? '',
                'editDate'   => $r['mcs_date_edition'] ?? null,
            ]]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /melis/react-api/cms-page/scripts/save — body JSON {idPage, headTop, headBottom, bodyBottom}.
     * Écrit les scripts custom de la page dans melis_cms_scripts (UPDATE la ligne existante, sinon INSERT).
     */
    public function saveAction(): HttpResponse
    {
        $sm = $this->getServiceManager();
        $auth = $sm->get('MelisCoreAuth');
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        try {
            $body = json_decode((string) $this->getRequest()->getContent(), true) ?: [];
            $idPage = (int) ($body['idPage'] ?? 0);
            if ($idPage <= 0) { return $this->json(['success' => false, 'error' => 'idPage requis'], 400); }
            $headTop    = (string) ($body['headTop'] ?? '');
            $headBottom = (string) ($body['headBottom'] ?? '');
            $bodyBottom = (string) ($body['bodyBottom'] ?? '');
            $userId = (int) ($auth->getIdentity()->usr_id ?? 0);
            $now = date('Y-m-d H:i:s');
            $db = $sm->get('Laminas\Db\Adapter\AdapterInterface');
            $existing = iterator_to_array($db->query('SELECT mcs_id FROM melis_cms_scripts WHERE mcs_page_id = ? ORDER BY mcs_id DESC LIMIT 1', [$idPage]));
            if (!empty($existing)) {
                $db->query('UPDATE melis_cms_scripts SET mcs_head_top = ?, mcs_head_bottom = ?, mcs_body_bottom = ?, mcs_date_edition = ?, mcs_user_id = ? WHERE mcs_id = ?',
                    [$headTop, $headBottom, $bodyBottom, $now, $userId, (int) $existing[0]['mcs_id']]);
            } else {
                $db->query('INSERT INTO melis_cms_scripts (mcs_page_id, mcs_head_top, mcs_head_bottom, mcs_body_bottom, mcs_date_edition, mcs_user_id) VALUES (?, ?, ?, ?, ?, ?)',
                    [$idPage, $headTop, $headBottom, $bodyBottom, $now, $userId]);
            }
            return $this->json(['success' => true, 'data' => ['idPage' => $idPage, 'editDate' => $now]]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function json(array $data, int $status = 200): HttpResponse
    {
        /** @var HttpResponse $r */
        $r = $this->getResponse();
        $r->setStatusCode($status);
        $r->getHeaders()->addHeaders(['Content-Type' => 'application/json; charset=utf-8', 'X-Content-Type-Options' => 'nosniff']);
        $r->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $r;
    }
}
