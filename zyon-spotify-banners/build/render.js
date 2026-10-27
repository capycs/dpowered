// Renders each banner HTML to a print PDF (vector, with bleed) and JPG previews.
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const here = __dirname;
const out = path.join(here, '..');
const only = process.argv[2];
const MM = 96 / 25.4;

async function open(browser, file, opts = {}) {
  const page = await browser.newPage(opts);
  await page.goto('file://' + path.join(here, file));
  await page.evaluate(() => document.fonts.ready);
  // Scale every data-fit text so it spans exactly its target width (mm).
  await page.evaluate(() => {
    for (const t of document.querySelectorAll('text[data-fit]')) {
      const size = parseFloat(t.getAttribute('font-size'));
      t.setAttribute('font-size', (size * parseFloat(t.dataset.fit) / t.getBBox().width).toFixed(2));
    }
    // Put the cap-height top of data-captop texts exactly where the layout asked.
    for (const t of document.querySelectorAll('text[data-captop]')) {
      const size = parseFloat(t.getAttribute('font-size'));
      t.setAttribute('y', (parseFloat(t.dataset.captop) + size * parseFloat(t.dataset.cap)).toFixed(2));
    }
  });
  return page;
}

(async () => {
  const browser = await chromium.launch();
  const files = fs.readdirSync(here).filter(f => f.endsWith('.html') && (!only || f.includes(only)));
  for (const f of files) {
    const name = f.replace('.html', '');
    const sub = name.startsWith('v2_') ? 'v2' : 'v1';
    for (const d of ['print', 'previews']) fs.mkdirSync(path.join(out, d, sub), { recursive: true });
    const page = await open(browser, f);
    const { w, h } = await page.evaluate(() => {
      const s = document.querySelector('svg');
      return { w: +s.dataset.w, h: +s.dataset.h };
    });
    await page.pdf({ path: path.join(out, 'print', sub, name + '.pdf'), width: w + 'mm', height: h + 'mm', printBackground: true });
    await page.close();

    // Previews ~1600px on the long edge; the _GUIDES one shows trim (magenta) and safe zone (dashed).
    const dsf = 1600 / Math.max(w, h) / MM;
    for (const guides of [false, true]) {
      const p = await open(browser, f, { deviceScaleFactor: dsf, viewport: { width: Math.ceil(w * MM), height: Math.ceil(h * MM) } });
      if (guides) await p.evaluate(() => document.body.classList.add('guides'));
      await p.screenshot({ path: path.join(out, 'previews', sub, name + (guides ? '_GUIDES' : '') + '.jpg'), type: 'jpeg', quality: 90 });
      await p.close();
    }
    console.log('rendered', name, w + 'x' + h + 'mm');
  }
  await browser.close();
})();
