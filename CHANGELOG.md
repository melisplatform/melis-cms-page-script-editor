# Changelog

## v6.0.5 - 2026-09-25
### Security
* **security:** key the Sites-tool Scripts tab on the Sites right (audit item 7.0)
* **security:** page-scripts React API uses the page-edition right, like the legacy tab (audit item 7.0)

## v6.0.4 - 2026-09-23
### Security
* **security:** parameterised SQL for date filters, ORDER BY and hand-quoted values (audit item 11.0)

## v6.0.3 - 2026-08-20
### Security
* Gate mutating/data actions in legacy tool controllers (CWE-862)
### Docs
* **melisai:** React back-office AI documentation for MelisCmsPageScriptEditor

## v6.0.1 - 2026-08-10
### Dependencies & build
* **composer:** update docs/homepage links, swap zf2 keyword for laminas, bump php constraint to ^8.3|^8.5

## v6.0.0 - 2026-08-10
### Security
* **security:** add SECURITY.md (private vulnerability reporting policy)
* Fix audit findings
### Added
* **marketplace:** React back-office screenshots (/react)
* **cms-react:** droits/capabilities éditeur de page CMS + polish
* **cms-react:** endpoint scripts/save pour l'onglet Scripts éditable (upsert melis_cms_scripts)
* **react:** onglet Scripts natif pour l'editeur de page CMS (react-api)
* **react:** ship the "Scripts" site-editor tab as a module brick
* **react:** JSON API for the native React "Scripts" tab of the CMS Site editor
* Add MelisAI module documentation for AI consumption
### Fixed
* **react:** site Scripts tab no longer shows a red "HTTP 200" notification on save (ticket 0010861)
### Changed
* Save page form validation issue fixed
### Dependencies & build
* **composer:** bump melis-core/melis-cms constraint to ^6.0
### Docs
* **meliscmspagescripteditor:** rewrite as two-part doc (functional guide + technical reference with examples)

## v5.3.0 - 2024-09-25
### Changed
* User role display
* Datatable response
* Check issue 6358
### Dependencies & build
* Rebuilt the asset bundle

## v5.2.0 - 2024-06-06
* Maintenance release.

## v5.1.0 - 2024-02-13
### Changed
* Use uri in registering listener
* $listener property
* Missing translator variable

## v5.0.0 - 2022-06-22
### Changed
* Changed deprecated ArraySerializable to ArraySerializableHydrator
* Removed get_object_vars
