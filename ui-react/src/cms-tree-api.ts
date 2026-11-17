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

/** HTML entities → text. A <textarea> parses its content as text only: nothing is executed. */
function decodeEntities(s: string): string {
  if (!s.includes('&')) return s
  const ta = document.createElement('textarea')
  ta.innerHTML = s
  return ta.value
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
    // Les titres arrivent échappés HTML (« l&#039;été ») : on les décode, React ré-échappe à l'affichage
    // (Mantis #0011510).
    return nodes.map((n) => ({
      key: n.key,
      title: typeof n.title === 'string' ? decodeEntities(n.title.replace(/^\s*<i\b[^>]*><\/i>\s*/i, '').trim()) : n.title,
      lazy: !!n.lazy,
    }))
  } catch {
    return []
  }
}
