<?php

/**
 * Capacités React apportées par MelisCmsPageScriptEditor — droits avancés du back-office React.
 *
 * MODULARITÉ : ce module CONTRIBUE son onglet « Scripts » à l'éditeur de page CMS sous la MÊME clé
 * `meliscms_page` que MelisCms/SmallBusiness — Laminas\Stdlib\ArrayUtils::merge fusionne les `tabs`
 * (append). L'onglet devient ainsi gatable dans Users→Droits (nœud « Edition de page »), sans que
 * MelisCms connaisse ce module. La `key` DOIT être le melisKey de l'onglet (= son cap côté gating
 * React, cf. TAB_CAP fallthrough dans MelisReactApiPageController). Mergé dans Module::getConfig().
 */

return [
    'melisReactToolCapabilities' => [
        'meliscms_page' => [
            'tabs' => [
                ['key' => 'meliscms_page_script_editor', 'label' => 'tr_meliscmspagescripteditor_title'],
            ],
        ],
    ],
];
