import sharp from 'sharp';
import { readFile, writeFile, unlink, rename } from 'fs/promises';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';
import CleanCSS from 'clean-css';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const imgDir = join(root, 'img');
const cssDir = join(root, 'css');

const heroVariants = [
  { file: 'fv-info.png', out: 'fv-info-1140.png', width: 1140, quality: 78 },
  { file: 'fv-info-sp.png', out: 'fv-info-sp-500.png', width: 500, quality: 76 },
  { file: 'fv-info-sp.png', out: 'fv-info-sp-750.png', width: 750, quality: 78 },
  { file: 'top_mv_img_3_3.png', out: 'top_mv_img_3_3-sm.png', width: 400, quality: 80 },
];

for (const v of heroVariants) {
  const input = join(imgDir, v.file);
  const output = join(imgDir, v.out);
  await sharp(input)
    .rotate()
    .resize({ width: v.width, withoutEnlargement: true })
    .png({ quality: v.quality, compressionLevel: 9, palette: true })
    .toFile(output);
  const webpOut = output.replace('.png', '.webp');
  await sharp(output).webp({ quality: 72, effort: 6 }).toFile(webpOut);
  console.log('created', v.out);
}

// Smaller PNG fallback for company photo
const topMv = join(imgDir, 'top_mv_img_3_3.png');
const topMvTmp = topMv + '.tmp';
await sharp(topMv)
  .rotate()
  .resize({ width: 560, withoutEnlargement: true })
  .png({ quality: 72, compressionLevel: 9, palette: true, colors: 128 })
  .toFile(topMvTmp);
await unlink(topMv);
await rename(topMvTmp, topMv);
await sharp(topMv).webp({ quality: 68, effort: 6 }).toFile(join(imgDir, 'top_mv_img_3_3.webp'));

// Re-compress main hero PNGs used as default src
for (const f of ['fv-info.png', 'fv-info-sp.png']) {
  const input = join(imgDir, f);
  const tmp = input + '.tmp';
  const meta = await sharp(input).metadata();
  const w = f.includes('-sp') ? 750 : 1140;
  await sharp(input)
    .rotate()
    .resize({ width: Math.min(meta.width, w), withoutEnlargement: true })
    .png({ quality: 76, compressionLevel: 9, palette: true })
    .toFile(tmp);
  await unlink(input);
  await rename(tmp, input);
}

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
