/**
 * Fail the build when the version is not the same in all four places.
 *
 * The theme carries its version in style.css (the only file WordPress actually
 * reads it from), readme.txt, changelog.txt and package.json. Nothing checked
 * that they agreed, and they had already drifted: readme.txt's Stable tag sat
 * at 1.2.19 while the theme shipped 1.2.20.
 */
import { readFile } from 'node:fs/promises';
import path from 'node:path';

const ROOT = path.resolve(import.meta.dirname, '..');
const read = (f) => readFile(path.join(ROOT, f), 'utf8');

const style = (await read('style.css')).match(/^\s*Version:\s*(.+)$/m)?.[1].trim() ?? '';
const readme = (await read('readme.txt')).match(/^Stable tag:\s*(.+)$/m)?.[1].trim() ?? '';
// changelog.txt is newest-first, so the first "= x.y.z =" heading is the release.
const changelog = (await read('changelog.txt')).match(/^=\s*([0-9][^=\s]*)\s*=/m)?.[1].trim() ?? '';
const pkg = JSON.parse(await read('package.json')).version ?? '';

const rows = [
  ['style.css      Version', style],
  ['readme.txt     Stable tag', readme],
  ['changelog.txt  latest entry', changelog],
  ['package.json   version', pkg],
];
rows.forEach(([label, v]) => console.log(`  ${label.padEnd(30)} ${v || '(missing)'}`));

const unique = [...new Set(rows.map(([, v]) => v))];
if (unique.length !== 1 || !unique[0]) {
  console.error(`\n  version mismatch: ${unique.join(' / ')}`);
  process.exit(1);
}
console.log(`\n  versions agree: ${style}`);
