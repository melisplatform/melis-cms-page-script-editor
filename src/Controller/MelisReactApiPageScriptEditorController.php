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
    public function getAction(): HttpResponse
    {
        if (!$this->getServiceManager()->get('MelisCoreAuth')->hasIdentity()) {
            return $this->json(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
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
