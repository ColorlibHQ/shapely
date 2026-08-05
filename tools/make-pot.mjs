/**
 * Regenerate languages/shapely.pot.
 *
 * Uses WP-CLI's `wp i18n make-pot`, the tool WordPress.org itself uses. This
 * replaces grunt-wp-i18n (unmaintained) and wp-pot (npm-deprecated: "Package no
 * longer supported"), which between them accounted for every remaining
 * deprecation warning in the dependency tree.
 *
 * WP-CLI is not an npm package, so it is invoked from PATH.
 * Install: https://wp-cli.org/#installing
 */
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import { readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';

const run = promisify(execFile);
const ROOT = path.resolve(import.meta.dirname, '..');
const DEST = path.join(ROOT, 'languages', 'shapely.pot');

const styleCss = await readFile(path.join(ROOT, 'style.css'), 'utf8');
const version = styleCss.match(/^\s*Version:\s*(.+)$/m)?.[1].trim() ?? '';

try {
  await run('wp', ['--version']);
} catch {
  console.error(
    'WP-CLI not found on PATH.\n' +
    'languages/shapely.pot is generated with `wp i18n make-pot`.\n' +
    'Install WP-CLI: https://wp-cli.org/#installing'
  );
  process.exit(1);
}

const args = [
  'i18n', 'make-pot', ROOT, DEST,
  '--domain=shapely',
  '--package-name=' + `Shapely ${version}`.trim(),
  '--exclude=node_modules,vendor,build,tools,src,.github',
  '--headers=' + JSON.stringify({
    'Report-Msgid-Bugs-To': 'https://www.colorlib.com/',
    'Last-Translator': 'Colorlib <office@colorlib.com>',
    'Language-Team': 'Colorlib <office@colorlib.com>',
  }),
  '--allow-root',
];

const { stdout, stderr } = await run('wp', args, { cwd: ROOT, maxBuffer: 32 * 1024 * 1024 });
if (stderr.trim()) console.error(stderr.trim());
if (stdout.trim()) console.log(stdout.trim());

// Normalise line endings so diffs stay readable across platforms.
const pot = await readFile(DEST, 'utf8');
if (pot.includes('\r\n')) await writeFile(DEST, pot.replace(/\r\n/g, '\n'), 'utf8');

const strings = (pot.match(/^msgid /gm) || []).length - 1; // minus the header entry
console.log(`languages/shapely.pot — ${strings} translatable strings (package: Shapely ${version})`);
