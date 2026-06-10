---
title: MelisCmsPageScriptEditor module
package: melisplatform/melis-cms-page-script-editor
doc_type: module-documentation
audience: [users, developers, ai]
language: en
module_version: unversioned
last_reviewed: 2026-06-08
maintainer: Melis Technology
keywords: [scripts, styles, head, body, tracking, analytics, page, site, cms, melis, back-office, injection]
screenshots_dir: ./images
---

# MelisCmsPageScriptEditor — Functional & Technical Documentation (for AI)

> **What this is.** MelisCmsPageScriptEditor lets you **add custom scripts/styles** (analytics
> tags, tracking pixels, third-party widgets, custom `<script>`/`<style>`) to your pages and
> sites, and have them **injected automatically into the rendered HTML** — at the top of
> `<head>`, the bottom of `<head>`, or the bottom of `<body>`. You manage them from a **Scripts
> tab** in the page editor and another in the Site tool.
>
> **Two parts:** **[Part A — Functional Guide](#part-a--functional-guide)** (users) ·
> **[Part B — Technical Reference](#part-b--technical-reference)** (developers/AI, with examples).
> Consumed by the **MelisAI** MCP; the **[Screenshot index](#screenshot-index)** maps filenames.
> Reviewed 2026-06-08.

---
---

# PART A — Functional Guide

## A1. What it lets you do

- **Add code to your site without editing templates** — paste an analytics snippet, a chat
  widget, a custom style… and it appears on the live pages.
- **Choose where it goes** — three slots: **Head top** (right after `<head>`), **Head bottom**
  (just before `</head>`), **Body bottom** (just before `</body>`).
- **Scope it** — apply a script to the **whole site**, or to a **single page**.
- **Make exceptions** — let a specific page **opt out** of the site-wide scripts.

> **Important — turn it on per site:** the feature only injects scripts on a site once the module
> is **loaded on that site** (MelisCms → Sites → Module loading). Until then you can edit scripts
> in the back-office but nothing is injected on the front.

## A2. Two Scripts tabs — and how they differ

The module adds a **Scripts** tab in **two** places. They share the same three script slots but
have **different scope**:

### Page editor → Scripts tab (this page)

**Where:** open a page in the editor → **Scripts** tab.

Here you set the script slots for **this page only**, and you can tick **"exclude site scripts"**
so this page ignores the site-wide scripts. From a page you can **only** see/set whether *this*
page is an exception — there's no list of other pages here.

![Scripts tab in the page editor](./images/meliscmspagescripteditor-page-tab-scripts.png)
*The page editor's Scripts tab — head-top/head-bottom/body-bottom for this page, plus the
"exclude site scripts" toggle.*

### Site tool → Scripts tab (the whole site + all exceptions)

**Where:** MelisCms → **Sites** → open a site → **Scripts** tab.

Here you set the **site-wide** scripts (applied to every page), and you see the **full list of
exceptions** — every page across the site that opted out of the site scripts — which you can
manage (add/remove). This site-wide overview is something you cannot get from an individual page.

![Scripts tab in the Site tool](./images/meliscmspagescripteditor-tooloverride-sites-edit-tab-scripts.png)
*The Site tool's Scripts tab — the site-wide scripts plus the exceptions list.*

## A3. How the slots combine

When a page is served, the visitor gets: **site scripts first, then the page's own scripts**, in
each slot — **unless** the page excludes site scripts, in which case only the page's own scripts
are injected.

## A4. Common tasks — "How do I…?"

- **Add Google Analytics to a whole site** → Sites → open the site → **Scripts** tab → paste the
  snippet in *Head bottom* (and make sure the module is loaded on the site).
- **Add a one-off script to a single page** → page editor → **Scripts** tab.
- **Stop the site scripts on one page** → that page's **Scripts** tab → tick *exclude site scripts*.
- **See which pages excluded the site scripts** → Sites → the site's **Scripts** tab → the exceptions list.

---
---

# PART B — Technical Reference

## B1. Metadata & dependencies

| Item | Value |
|---|---|
| Package | `melisplatform/melis-cms-page-script-editor` · category `cms` · namespace `MelisCmsPageScriptEditor\` · dbdeploy |
| Requires | `melis-core`, `melis-cms` (`^5.2`) (README also lists engine/front at runtime) |

> **License note:** `composer.json` says OSL-3.0 while the README references the Melis premium
> EULA — flag to a human if it matters.

## B2. Data model

| Table | Role | PK |
|---|---|---|
| `melis_cms_scripts` | A script set for a site/page: `mcs_site_id`, `mcs_page_id`, `mcs_head_top`, `mcs_head_bottom`, `mcs_body_bottom`, edition date, user | `mcs_id` |
| `melis_cms_scripts_exceptions` | A page excluding its site's scripts: `mcse_site_id`, `mcse_page_id`, creation date, user | `mcse_id` |

## B3. Service `MelisCmsPageScriptEditorService` (with examples)

```php
$scripts = $this->getServiceManager()->get('MelisCmsPageScriptEditorService');

// The render-ready scripts for a page (site + page, applying exclusions, site first):
$resolved = $scripts->getMixedScriptsPerPage($pageId);

$scripts->addScript($siteId, $pageId, $headTop, $headBottom, $bodyBottom, $mcsId);
$scripts->addScriptException($siteId, $pageId);     // make a page an exception
$exc = $scripts->getScriptExceptions($siteId, $sortCol, $sortOrder);
$site = $scripts->getScriptsPerSite($siteId);
$page = $scripts->getScriptsPerPage($pageId);
```

Methods: `addScript`, `addScriptException`, `getScriptExceptions`, `getScriptsPerSite`,
`getScriptsPerPage`, `getScriptsExceptionPerPage`, **`getMixedScriptsPerPage`** (the resolver
used at render), `updatePageScripts`, `getSiteId`.

## B4. Injection at render & the listeners

Wired in `src/Module.php` (back-office vs front split):

| Listener | Trigger | Role |
|---|---|---|
| `MelisCmsPageScriptEditorSavePageListener` | Page save/publish (BO) | Persists the page's scripts + its exclude-site exception |
| `MelisCmsPageScriptEditorSaveSiteScriptListener` | Site save (BO) | Persists the site's scripts |
| `MelisCmsPageScriptEditorDuplicatePageListener` | Page duplicate (BO) | Copies scripts to the new page |
| `MelisCmsPageScriptEditorScriptTagListener` | Page render (front) | Injects the resolved scripts: head-top after `<head>`, head-bottom before `</head>`, body-bottom before `</body>` |

The render listener calls `getMixedScriptsPerPage()` to compute the effective output (site first,
unless excluded). View helper `melisCmsPageScriptEditorAddScript` performs the injection.

## B5. The two tabs

Both inject into the host UIs via `config/app.toolstree.php`:
- **Page editor tab** → `meliscmspagescripteditor_page_edition` (form
  `meliscmspagescripteditor_script_form` = the 3 textareas; exclude form
  `meliscmspagescripteditor_script_exception_form` = `mcse_exclude_site_scripts`).
- **Site tool tab** → `meliscms_tool_sites_scripts` (same script form for the whole site + an
  **exceptions DataTable** `meliscmspagescripteditor_site_script_exceptions` with a delete action,
  and an add-exception form `meliscmspagescripteditor_tool_site_exception_form`).
Controllers: `MelisCmsPageScriptEditorPageEditionController`,
`MelisCmsPageScriptEditorToolSiteEditionController`. Table gateways: `MelisCmsScriptTable`,
`MelisCmsScriptExceptionTable`.

## B6. Quick code map

```
melis-cms-page-script-editor/
├── config/   module.config.php · app.toolstree.php (the 2 Scripts tabs) · app.interface.php
│            · app.tools.php (forms + exceptions table)
├── src/   Controller/ (PageEdition, ToolSiteEdition) · Service/MelisCmsPageScriptEditorService
│        · Model/Tables/ (Script, ScriptException) · Listener/ (SavePage, SaveSiteScript, DuplicatePage, ScriptTag)
│        · View/Helper/MelisCmsPageScriptEditorAddScriptHelper
├── view/ · public/ · language/ · install/ (SQL)
└── etc/   MarketPlace + MelisAI/doc (this doc)
```

---

## Screenshot index

| Image file | Content |
|---|---|
| `meliscmspagescripteditor-page-tab-scripts.png` | Scripts tab in the page editor (this page + exclude toggle) |
| `meliscmspagescripteditor-tooloverride-sites-edit-tab-scripts.png` | Scripts tab in the Site tool (site-wide + exceptions list) |

---

*Document for AI consumption (MelisAI MCP) — `melisplatform/melis-cms-page-script-editor`. Part A
= functional; Part B = technical with examples. Last reviewed 2026-06-08.*
