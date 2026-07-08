import { defineConfig } from 'vite'
import path from 'node:path'

/**
 * Build for the MelisCmsPageScriptEditor React brick.
 *
 * IIFE bundle (public/ui-react/brick.js) loaded at runtime by the MelisCore shell when the module
 * is active. It's a CONTRIBUTION-ONLY brick: it registers a "Scripts" tab into the CMS Site editor
 * (via the generic window.__melisSiteTabs registry exposed by the MelisCms brick) — no route/menu.
 * React is EXTERNAL (host globals from MelisCore's main.tsx) so the shared React instance is reused.
 */
export default defineConfig({
  esbuild: { jsx: 'automatic' },
  build: {
    outDir: path.resolve(import.meta.dirname, '..', 'public', 'ui-react'),
    emptyOutDir: false,
    lib: {
      entry: path.resolve(import.meta.dirname, 'src/brick.tsx'),
      formats: ['iife'],
      name: 'MelisCmsPageScriptEditorBrick',
      fileName: () => 'brick.js',
    },
    rollupOptions: {
      external: ['react', 'react-dom', 'react/jsx-runtime', 'react-router-dom'],
      output: {
        globals: {
          react: 'MelisReact',
          'react-dom': 'MelisReactDOM',
          'react/jsx-runtime': 'MelisReactJsxRuntime',
          'react-router-dom': 'MelisReactRouterDOM',
        },
      },
    },
  },
})
