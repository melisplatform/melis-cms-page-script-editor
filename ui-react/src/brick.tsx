import ScriptsTab from './ScriptsTab'

/**
 * Point d'entrée de la brique MelisCmsPageScriptEditor.
 *
 * Brique de CONTRIBUTION (pas de route/menu) : elle enregistre l'onglet « Scripts » dans l'éditeur
 * de site de MelisCms, via le registre GÉNÉRIQUE window.__melisSiteTabs (contrat défini côté MelisCms,
 * cf. site-tab-registry.ts). Aucun import cross-bundle : on écrit directement le global + on émet un
 * évènement pour que les SiteEditor déjà montés se rafraîchissent. La brique n'est chargée que si le
 * module est actif (discovery /react-modules + prefetch de loadBricks) → l'onglet n'apparaît que dans
 * ce cas, comme l'injection de config du legacy.
 */

interface SiteTabDef {
  id: string
  label: string | { fr: string; en: string }
  order?: number
  Component: unknown
}

declare global {
  interface Window {
    __melisSiteTabs?: SiteTabDef[]
  }
}

function registerSiteTab(tab: SiteTabDef) {
  const list = (window.__melisSiteTabs ??= [])
  const i = list.findIndex((t) => t.id === tab.id)
  if (i >= 0) list[i] = tab
  else list.push(tab)
  window.dispatchEvent(new CustomEvent('melis:site-tabs-changed'))
}

registerSiteTab({
  id: 'scripts',
  label: { fr: 'Scripts', en: 'Scripts' },
  order: 55, // après « Config du site », avant « Traductions » (comme le legacy)
  Component: ScriptsTab,
})
