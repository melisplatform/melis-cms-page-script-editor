/**
 * Arbre de pages CMS (pour le sélecteur « sitemap » de l'ajout d'exception).
 *
 * Réutilise l'endpoint legacy lazy (aucun changement backend) :
 *   GET /melis/MelisCms/TreeSites/get-tree-pages-by-page-id?nodeId=<id>
 *   nodeId = -1 → racines (sites) ; nodeId = <pageId> → enfants de cette page.
 * `key` = id de page. (Version minimale — juste ce qu'il faut pour le picker de la brique.)
 */
export interface MelisTreeNode {
  key: number
  title: string
  lazy: boolean
}

export async function fetchTreeNodes(nodeId: number): Promise<MelisTreeNode[]> {
  try {
    const res = await fetch(
      `/melis/MelisCms/TreeSites/get-tree-pages-by-page-id?nodeId=${encodeURIComponent(String(nodeId))}`,
      { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'include' },
    )
    if (!res.ok) return []
    const data = await res.json()
    let nodes: MelisTreeNode[] = []
    if (Array.isArray(data)) nodes = data as MelisTreeNode[]
    else if (Array.isArray(data?.data)) nodes = data.data as MelisTreeNode[]
    else if (Array.isArray(data?.tree)) nodes = data.tree as MelisTreeNode[]
    // Le listener de verrou legacy préfixe parfois le titre par une icône <i> HTML : on la retire
    // (React échappe le HTML, il s'afficherait en texte).
    return nodes.map((n) => ({
      key: n.key,
      title: typeof n.title === 'string' ? n.title.replace(/^\s*<i\b[^>]*><\/i>\s*/i, '').trim() : n.title,
      lazy: !!n.lazy,
    }))
  } catch {
    return []
  }
}
