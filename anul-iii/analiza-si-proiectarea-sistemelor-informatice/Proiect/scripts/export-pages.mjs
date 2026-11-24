import { mkdir, readFile, rm, writeFile, cp } from 'node:fs/promises';
import path from 'node:path';

const origin = process.env.EXPORT_ORIGIN ?? 'http://127.0.0.1:8000';
const basePath = (process.env.PAGES_BASE_PATH ?? '').replace(/\/$/, '');
const output = path.resolve('dist-pages');
const categories = ['hosting', 'wordpress', 'vps', 'cloud', 'domain', 'email', 'backup', 'ssl', 'apps', 'dedicated', 'gpu', 'managed', 'storage', 'database', 'security', 'devops'];
const routes = ['/', '/hosting', '/vps', '/domenii', '/email', '/cloud', '/wordpress', '/backup', '/ssl', '/despre', '/about', '/contact', '/termeni', '/confidentialitate', '/cookies', '/retragere', '/reclamatii', ...categories.map((category) => `/servicii/${category}`)];

for (let attempt = 0; attempt < 30; attempt++) {
  try {
    if ((await fetch(origin)).ok) break;
  } catch {}
  if (attempt === 29) throw new Error('Laravel did not become available for the static export.');
  await new Promise((resolve) => setTimeout(resolve, 1000));
}

await rm(output, { recursive: true, force: true });
await mkdir(output, { recursive: true });
await cp('public/build', path.join(output, 'build'), { recursive: true });
for (const item of ['favicon.ico', 'robots.txt', 'images']) {
  try { await cp(path.join('public', item), path.join(output, item), { recursive: true }); } catch {}
}

async function exportLocale(locale) {
  let cookie = '';
  if (locale === 'en') {
    const response = await fetch(`${origin}/language/en`, { redirect: 'manual', headers: { referer: `${origin}/` } });
    cookie = response.headers.get('set-cookie')?.split(';')[0] ?? '';
  }

  for (const route of routes) {
    const pageUrl = locale === 'en' ? route === '/' ? '/?pages_locale=en' : `${route}?pages_locale=en` : route;
    const response = await fetch(new URL(pageUrl, origin), { headers: cookie ? { cookie } : {} });
    if (!response.ok) throw new Error(`Export failed (${response.status}): ${pageUrl}`);
    let html = await response.text();
    const pageRoute = route === '/' ? '' : route;
    const localeRoot = locale === 'en' ? '/en' : '';
    html = html.replace(/(href|src)="([^"]+)"/g, (match, attribute, rawValue) => {
      let value = rawValue;
      if (rawValue.startsWith(`${origin}/`) || rawValue === origin) value = new URL(rawValue, origin).pathname.replace(/^\//, '');
      else if (rawValue.startsWith('/') && !rawValue.startsWith('//')) value = rawValue.replace(/^\//, '');
      else return match;
      if (value.startsWith('build/') || value.startsWith('favicon') || value.startsWith('images/')) return `${attribute}="${basePath}/${value}"`;
      if (value.startsWith('language/')) {
        const switchTo = value.split('/')[1];
        const target = switchTo === 'en' ? `/en${pageRoute || '/'}` : pageRoute || '/';
        return `${attribute}="${basePath}${target}"`;
      }
      if (value.startsWith('login') || value.startsWith('register') || value.startsWith('client/') || value.startsWith('admin/')) return `${attribute}="${basePath}/portal/"`;
      const suffix = value.match(/[?#].*$/)?.[0] ?? '';
      const cleanPath = value.replace(/[?#].*$/, '');
      const targetPath = `${localeRoot}/${cleanPath}`.replace(/\/{2,}/g, '/');
      const target = `${basePath}${targetPath.endsWith('/') ? targetPath : `${targetPath}/`}${suffix}`;
      return `${attribute}="${target}"`;
    });

    if (route === '/contact') {
      const note = locale === 'ro'
        ? '<p class="pages-notice">Aceasta este o previzualizare statică. Formularul de contact este disponibil în aplicația completă.</p>'
        : '<p class="pages-notice">This is a static preview. The contact form is available in the full application.</p>';
      html = html.replace('<form ', `${note}<form `).replace(/<button class="button button-primary form-submit"/, '<button disabled class="button button-primary form-submit"');
      html = html.replace(/action="[^"]*"/g, 'action="#"').replace(/<input type="hidden" name="_token"[^>]*>/g, '');
    }

    const relativePath = path.join(output, locale === 'en' ? 'en' : '', route.replace(/^\//, ''), 'index.html');
    await mkdir(path.dirname(relativePath), { recursive: true });
    await writeFile(relativePath, html);
  }
}

await exportLocale('ro');
await exportLocale('en');
const manifest = JSON.parse(await readFile('public/build/manifest.json', 'utf8'));
const cssPath = path.join(output, 'build', manifest['resources/css/app.css'].file);
const css = await readFile(cssPath, 'utf8');
await writeFile(cssPath, `${css}\n.pages-notice{margin:0 0 18px;padding:12px 14px;border-left:3px solid #63845e;background:#e4eadc;color:#263f31;font-size:13px;line-height:1.5}html[data-theme="dark"] .pages-notice{background:#263c2e;color:#d9e9d6;border-color:#b9dc62}`);

const portalPath = path.join(output, 'portal', 'index.html');
await mkdir(path.dirname(portalPath), { recursive: true });
await writeFile(portalPath, `<!doctype html><html lang="ro"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Portal client — ELOQUENCE HOSTING</title><link rel="stylesheet" href="${basePath}/build/assets/app.css"><main class="public-page-hero"><h1>Portal client</h1><p>Portalul și funcțiile de cont necesită aplicația Laravel găzduită separat.</p><a class="button button-primary" href="${basePath}/">Înapoi la site</a></main></html>`);
const englishPortalPath = path.join(output, 'en', 'portal', 'index.html');
await mkdir(path.dirname(englishPortalPath), { recursive: true });
await writeFile(englishPortalPath, `<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Client portal — ELOQUENCE HOSTING</title><link rel="stylesheet" href="${basePath}/build/assets/app.css"><main class="public-page-hero"><h1>Client portal</h1><p>Account and portal features require the Laravel application hosted separately.</p><a class="button button-primary" href="${basePath}/en/">Back to website</a></main></html>`);
await writeFile(path.join(output, '.nojekyll'), '');
console.log(`Exported ${routes.length * 2} public pages to ${output}`);
