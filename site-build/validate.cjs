require('global-jsdom/register');
const fs = require('fs');
window.matchMedia = window.matchMedia || (() => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} }));
globalThis.requestAnimationFrame = globalThis.requestAnimationFrame || ((cb) => setTimeout(cb, 0));
const blocks = require('@wordpress/blocks');
const lib = require('@wordpress/block-library');
lib.registerCoreBlocks();
const files = process.argv.slice(2);
let bad = 0;
function walk(list, file, path = []) {
  for (const b of list) {
    if (!b.isValid) {
      bad++;
      console.log(`INVALID ${file} :: ${[...path, b.name].join(' > ')}`);
      for (const i of (b.validationIssues || []).slice(0, 3)) console.log('   ', i.args ? i.args.map(a => typeof a === 'string' ? a.slice(0, 300) : JSON.stringify(a)?.slice(0,300)).join(' | ') : i);
    }
    if (b.name === 'core/missing' || b.name === 'core/freeform') { bad++; console.log(`UNKNOWN ${file} :: ${b.name}`); }
    walk(b.innerBlocks || [], file, [...path, b.name]);
  }
}
for (const f of files) {
  const html = fs.readFileSync(f, 'utf8');
  const parsed = blocks.parse(html);
  walk(parsed, f);
}
console.log(bad ? `FAIL: ${bad} invalid block(s) in ${files.length} file(s)` : `OK: ${files.length} file(s), all blocks valid`);
process.exit(bad ? 1 : 0);
