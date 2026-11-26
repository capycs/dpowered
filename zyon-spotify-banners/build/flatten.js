// Renders an all-raster layer page (e.g. zbg_6x2.html) to one flat JPEG at a given dpi.
// Usage: node flatten.js <page-name> <out.jpg> <dpi>
const { chromium } = require('playwright');
const path = require('path');

const [name, out, dpi] = process.argv.slice(2);
const MM = 96 / 25.4;

(async () => {
  const browser = await chromium.launch();
  const probe = await browser.newPage();
  await probe.goto('file://' + path.join(__dirname, name + '.html'));
  const { w, h } = await probe.evaluate(() => {
    const s = document.querySelector('svg');
    return { w: +s.dataset.w, h: +s.dataset.h };
  });
  await probe.close();
  const page = await browser.newPage({
    deviceScaleFactor: parseFloat(dpi) / 96,
    viewport: { width: Math.round(w * MM), height: Math.round(h * MM) },
  });
  await page.goto('file://' + path.join(__dirname, name + '.html'));
  await page.screenshot({ path: path.join(__dirname, out), type: 'jpeg', quality: 92 });
  await browser.close();
  console.log('flattened', name, '->', out, w + 'x' + h + 'mm @', dpi, 'dpi');
})();
