import { readFile, writeFile } from 'fs/promises';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';
import CleanCSS from 'clean-css';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const cssDir = join(root, 'css');

const cssFiles = ['style.css', 'top.css', 'perf.css'];
let combined = '';
for (const f of cssFiles) {
  combined += await readFile(join(cssDir, f), 'utf8');
  combined += '\n';
}

const minified = new CleanCSS({ level: 2 }).minify(combined);
if (minified.errors.length) {
  console.error(minified.errors);
  process.exit(1);
}
await writeFile(join(cssDir, 'main.min.css'), minified.styles);
console.log('main.min.css', Math.round(minified.styles.length / 1024), 'KB');
