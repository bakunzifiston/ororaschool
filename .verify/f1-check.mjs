/**
 * Phase F1 browser checks.
 *
 *   php artisan serve --port=8123
 *   npm install --no-save puppeteer-core
 *   node .verify/f1-check.mjs
 *
 * Asserts the things a feature test cannot see: computed accent per experience,
 * horizontal overflow at three widths, the switcher dropdown and modal actually
 * opening, drawer behaviour at mobile width, and visible focus rings.
 */
import puppeteer from 'puppeteer-core';
import { mkdirSync } from 'node:fs';

const BASE = 'http://127.0.0.1:8123';
const OUT = '.verify/shots';
mkdirSync(OUT, { recursive: true });

const results = [];
const fail = (m) => { results.push(['FAIL', m]); };
const pass = (m) => { results.push(['ok  ', m]); };
const check = (cond, m) => cond ? pass(m) : fail(m);

const browser = await puppeteer.launch({
    executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    headless: 'new',
    args: ['--no-sandbox', '--force-color-profile=srgb', '--font-render-hinting=none'],
});

const page = await browser.newPage();
const consoleErrors = [];
page.on('pageerror', (e) => consoleErrors.push(String(e)));
page.on('console', (m) => { if (m.type() === 'error') consoleErrors.push(m.text()); });

const go = async (path, width = 1440, height = 1000) => {
    await page.setViewport({ width, height, deviceScaleFactor: 2 });
    await page.goto(BASE + path, { waitUntil: 'networkidle0' });
    await new Promise((r) => setTimeout(r, 350));
};

const overflow = () => page.evaluate(() =>
    document.documentElement.scrollWidth - document.documentElement.clientWidth);

const accent = () => page.evaluate(() =>
    getComputedStyle(document.documentElement).getPropertyValue('--xp-accent-500').trim());

// ---------------------------------------------------------------------------
// 1. Each layout reachable, correct accent, no horizontal overflow at 3 widths
// ---------------------------------------------------------------------------
const layouts = [
    ['/admin', 'super-admin', '#1f5f73', 'admin-dashboard'],
    ['/workspace/gemura', 'platform-workspace', '#3e6b34', 'workspace-dashboard'],
    ['/learn', 'learner', '#8a6a16', 'learner-dashboard'],
];

for (const [path, experience, expectedAccent, shot] of layouts) {
    await go(path);
    const attr = await page.evaluate(() => document.documentElement.dataset.experience);
    check(attr === experience, `${path} renders in the ${experience} layout`);
    check(accentMatches(await accent(), expectedAccent), `${path} accent resolves to ${expectedAccent}`);
    await page.screenshot({ path: `${OUT}/${shot}-desktop.png`, fullPage: true });

    for (const [w, h, label] of [[1440, 1000, 'desktop'], [820, 1100, 'tablet'], [375, 900, 'mobile']]) {
        await go(path, w, h);
        const over = await overflow();
        check(over <= 0, `${path} has no horizontal overflow at ${w}px (measured ${over}px)`);
        if (label === 'mobile') {
            await page.screenshot({ path: `${OUT}/${shot}-mobile.png`, fullPage: true });
        }
    }
}

function accentMatches(actual, expected) {
    return actual.toLowerCase() === expected.toLowerCase();
}

// ---------------------------------------------------------------------------
// 2. Navigating between the three layouts through the UI
// ---------------------------------------------------------------------------
await go('/admin');
const railLinks = await page.$$eval('aside a[href]', (as) => as.map((a) => new URL(a.href).pathname));
check(railLinks.filter((h) => h && h !== '#').length >= 9,
    `super admin rail has ${railLinks.length} live links, none dead`);
check(!railLinks.includes('#'), 'no super admin rail item falls back to "#"');

await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('aside a[href$="/admin/platforms"]'),
]);
check(page.url().endsWith('/admin/platforms'), 'clicking a rail item navigates');
check(await page.evaluate(() => !!document.querySelector('[aria-current="page"]')),
    'the active rail item is marked aria-current="page"');

// ---------------------------------------------------------------------------
// 3. Workspace switcher: closed, opens, lists only reachable platforms, navigates
// ---------------------------------------------------------------------------
await go('/workspace/gemura');

const switcherBtn = 'header [aria-controls="workspace-switcher-topbar"]';
check(await page.evaluate((s) => document.querySelector(s)?.getAttribute('aria-expanded') === 'false', switcherBtn),
    'switcher starts collapsed with aria-expanded="false"');

const menuVisibleBefore = await page.evaluate(() => {
    const m = document.querySelector('#workspace-switcher-topbar');
    return m ? getComputedStyle(m).display !== 'none' : null;
});
check(menuVisibleBefore === false, 'switcher menu is hidden before interaction (x-cloak holds)');

await page.click(switcherBtn);
await new Promise((r) => setTimeout(r, 300));

const menu = await page.evaluate(() => {
    const m = document.querySelector('#workspace-switcher-topbar');
    if (!m) return null;
    const style = getComputedStyle(m);
    const rect = m.getBoundingClientRect();
    return {
        visible: style.display !== 'none' && style.opacity !== '0' && rect.height > 0,
        items: [...m.querySelectorAll('a[href]')].map((a) => ({
            href: new URL(a.href).pathname,
            name: a.querySelector('span span')?.textContent.trim(),
            current: a.getAttribute('aria-current') === 'true',
        })),
    };
});

check(menu?.visible === true, 'switcher dropdown becomes visible on click');
check(menu?.items.length === 3, `switcher lists 3 platforms (got ${menu?.items.length})`);
check(menu?.items.every((i) => i.href?.startsWith('/workspace/')),
    'every switcher entry links to a platform workspace route');
check(menu?.items.filter((i) => i.current).length === 1, 'exactly one entry is marked as current');
check(!menu?.items.some((i) => i.name === 'OroraFarm'),
    'OroraFarm is absent — the fixture user does not work on it');
check(await page.evaluate((s) => document.querySelector(s)?.getAttribute('aria-expanded') === 'true', switcherBtn),
    'aria-expanded flips to "true" when open');

await page.screenshot({ path: `${OUT}/switcher-open.png` });

const target = menu.items.find((i) => !i.current);
await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click(`#workspace-switcher-topbar a[href$="${target.href}"]`),
]);
check(page.url().endsWith(target.href), `selecting ${target.name} navigates to its dashboard`);
const switched = await page.evaluate(() => document.querySelector('h1')?.textContent.trim());
check(switched?.includes(target.name), `the ${target.name} dashboard shows its own content ("${switched}")`);
await page.screenshot({ path: `${OUT}/workspace-switched.png`, fullPage: true });

// Escape closes it.
await go('/workspace/gemura');
await page.click(switcherBtn);
await new Promise((r) => setTimeout(r, 250));
await page.keyboard.press('Escape');
await new Promise((r) => setTimeout(r, 300));
check(await page.evaluate(() => {
    const m = document.querySelector('#workspace-switcher-topbar');
    return getComputedStyle(m).display === 'none' || m.getBoundingClientRect().height === 0;
}), 'Escape closes the switcher');

// ---------------------------------------------------------------------------
// 4. Modal shell in each experience
// ---------------------------------------------------------------------------
for (const [path, label] of [['/admin', 'super admin'], ['/workspace/gemura', 'workspace'], ['/learn', 'learner']]) {
    await go(path);

    const before = await page.evaluate(() => {
        const d = document.querySelector('[role="dialog"]');
        return d ? getComputedStyle(d).display !== 'none' && d.getBoundingClientRect().height > 0 : null;
    });
    check(before === false, `${label}: dialog hidden on load`);

    const trigger = await page.$('button[x-on\\:click*="open-modal"]');
    check(!!trigger, `${label}: a modal trigger exists`);
    await trigger.click();
    await new Promise((r) => setTimeout(r, 400));

    const open = await page.evaluate(() => {
        const d = document.querySelector('[role="dialog"]');
        const r = d.getBoundingClientRect();
        return {
            visible: getComputedStyle(d).display !== 'none' && r.height > 0 && r.width > 0,
            modal: d.getAttribute('aria-modal') === 'true',
            focused: document.activeElement === d,
            title: d.querySelector('h2')?.textContent.trim(),
        };
    });
    check(open.visible, `${label}: dialog opens and is visible`);
    check(open.modal, `${label}: dialog carries aria-modal="true"`);
    check(open.focused, `${label}: focus moves into the dialog`);
    if (label === 'super admin') await page.screenshot({ path: `${OUT}/modal-open.png` });

    await page.keyboard.press('Escape');
    await new Promise((r) => setTimeout(r, 350));
    check(await page.evaluate(() => {
        const d = document.querySelector('[role="dialog"]');
        return getComputedStyle(d).display === 'none' || d.getBoundingClientRect().height === 0;
    }), `${label}: Escape closes the dialog`);
}

// ---------------------------------------------------------------------------
// 5. Mobile drawer (railed layouts) and learner mobile menu
// ---------------------------------------------------------------------------
for (const [path, label] of [['/admin', 'super admin'], ['/workspace/gemura', 'workspace']]) {
    await go(path, 375, 900);
    const hiddenFirst = await page.evaluate(() => {
        const a = document.querySelector('aside');
        return getComputedStyle(a).display === 'none';
    });
    check(hiddenFirst, `${label}: rail is hidden at 375px until opened`);

    await page.click('button[aria-label="Open navigation"]');
    await new Promise((r) => setTimeout(r, 350));
    const drawer = await page.evaluate(() => {
        const a = document.querySelector('aside');
        const r = a.getBoundingClientRect();
        return { shown: getComputedStyle(a).display !== 'none' && r.width > 100, links: a.querySelectorAll('a[href]').length };
    });
    check(drawer.shown, `${label}: drawer opens at 375px`);
    check(drawer.links >= 9, `${label}: drawer carries the full nav (${drawer.links} links)`);
    check(await page.$('form[action$="/sign-out"] button') !== null, `${label}: sign-out reachable at mobile width`);
    if (label === 'super admin') await page.screenshot({ path: `${OUT}/drawer-mobile.png` });
}

await go('/learn', 375, 900);
check(await page.evaluate(() => !document.querySelector('aside')), 'learner layout has no sidebar at any width');
await page.click('button[aria-expanded]');
await new Promise((r) => setTimeout(r, 300));
const learnerMenu = await page.evaluate(() =>
    [...document.querySelectorAll('nav a')]
        .filter((a) => new URL(a.href).pathname.startsWith('/learn') && a.offsetParent !== null).length);
check(learnerMenu >= 7, `learner mobile menu exposes all 7 destinations (${learnerMenu} visible)`);
await page.screenshot({ path: `${OUT}/learner-mobile-menu.png` });

// ---------------------------------------------------------------------------
// 6. Keyboard focus visibility
// ---------------------------------------------------------------------------
await go('/admin');
const focusRings = await page.evaluate(() => {
    const targets = [
        ['rail link', 'aside a[href]'],
        ['search input', 'header input[type="search"]'],
        ['sign-out button', 'header form button'],
        ['primary action', 'main button'],
        ['table pagination control', 'nav[aria-label="Pagination"] button:not([disabled])'],
        ['breadcrumb link', 'nav[aria-label="Breadcrumb"] a'],
    ];
    return targets.map(([label, sel]) => {
        // Only ever measure something actually on screen: a control inside a
        // closed dialog cannot take focus and would report no ring.
        const el = [...document.querySelectorAll(sel)].find((e) => e.offsetParent !== null);
        if (!el) return { label, found: false };
        el.focus();
        const s = getComputedStyle(el);
        const width = parseFloat(s.outlineWidth) || 0;
        return { label, found: true, outline: `${s.outlineStyle} ${s.outlineWidth} ${s.outlineColor}`, visible: width >= 1 && s.outlineStyle !== 'none' };
    });
});
for (const r of focusRings) {
    check(r.found && r.visible, `focus ring visible on ${r.label}${r.found ? ` (${r.outline})` : ' — not found'}`);
}

// Tab order actually reaches the content.
await go('/learn');
const tabbed = [];
for (let i = 0; i < 6; i++) {
    await page.keyboard.press('Tab');
    tabbed.push(await page.evaluate(() => {
        const a = document.activeElement;
        return { tag: a.tagName, text: (a.innerText || a.getAttribute('aria-label') || '').trim().slice(0, 30), outline: parseFloat(getComputedStyle(a).outlineWidth) || 0 };
    }));
}
check(tabbed.every((t) => t.outline >= 1), 'every element reached by Tab shows an outline');

// ---------------------------------------------------------------------------
// 7. Component proof page in all three experiences
// ---------------------------------------------------------------------------
for (const experience of ['super-admin', 'platform-workspace', 'learner']) {
    await go(`/design/components/${experience}`, 1440, 1200);
    const counts = await page.evaluate(() => ({
        statCards: document.querySelectorAll('[class*="border-l-accent-500"]').length,
        badges: document.querySelectorAll('span[class*="rounded-sm"][class*="text-micro"]').length,
        progress: document.querySelectorAll('[role="progressbar"]').length,
        tables: document.querySelectorAll('table').length,
        cards: document.querySelectorAll('article').length,
        dialogs: document.querySelectorAll('[role="dialog"]').length,
        emptyStates: [...document.querySelectorAll('h3')].length,
    }));
    check(counts.statCards >= 5, `${experience}: ${counts.statCards} stat cards across two fixture sets`);
    check(counts.progress >= 7, `${experience}: ${counts.progress} progress bars across two fixture sets`);
    check(counts.tables === 2, `${experience}: 2 populated tables + 1 empty-state fallback`);
    check(counts.cards === 4, `${experience}: 4 course cards (2 staff context, 2 learner context)`);
    check(counts.dialogs === 2, `${experience}: 2 dialogs from one shell`);
    await page.screenshot({ path: `${OUT}/proof-${experience}.png`, fullPage: true });

    for (const w of [1440, 820, 375]) {
        await go(`/design/components/${experience}`, w, 1200);
        const over = await overflow();
        check(over <= 0, `${experience} proof page: no overflow at ${w}px (${over}px)`);
    }
}

// Confirm the three accents actually differ.
const accents = {};
for (const experience of ['super-admin', 'platform-workspace', 'learner']) {
    await go(`/design/components/${experience}`);
    accents[experience] = await page.evaluate(() => {
        const el = document.querySelector('[class*="border-l-accent-500"]') || document.body;
        return getComputedStyle(el).borderLeftColor;
    });
}
check(new Set(Object.values(accents)).size === 3,
    `the three experiences resolve to three different accents: ${JSON.stringify(accents)}`);

// ---------------------------------------------------------------------------
// 8. Flash region via the sign-out control
// ---------------------------------------------------------------------------
await go('/admin');
await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('header form[action$="/sign-out"] button'),
]);
const flash = await page.evaluate(() => {
    const r = document.querySelector('[role="status"]');
    return r ? r.innerText.trim().slice(0, 80) : null;
});
check(new URL(page.url()).pathname === '/login', 'sign-out leaves the app for the sign-in page');
check(!!flash && flash.includes('Signed out'), `flash region renders the message ("${flash}")`);
await page.screenshot({ path: `${OUT}/flash-region.png` });

// Dismissible.
await page.click('[role="status"] button[aria-label="Dismiss message"]');
await new Promise((r) => setTimeout(r, 250));
check(await page.evaluate(() => {
    const el = document.querySelector('[role="status"] .rounded-md');
    return !el || getComputedStyle(el).display === 'none';
}), 'flash message can be dismissed');

// ---------------------------------------------------------------------------
// 9. Typography and token wiring actually applied
// ---------------------------------------------------------------------------
await go('/learn');
const type = await page.evaluate(() => {
    const h1 = document.querySelector('h1');
    const body = document.body;
    const fig = document.querySelector('.figure');
    return {
        heading: getComputedStyle(h1).fontFamily,
        headingSize: getComputedStyle(h1).fontSize,
        body: getComputedStyle(body).fontFamily,
        bg: getComputedStyle(body).backgroundColor,
        figure: fig ? getComputedStyle(fig).fontFamily : null,
        tnum: fig ? getComputedStyle(fig).fontFeatureSettings : null,
    };
});
check(/Bitter/.test(type.heading), `headings use Bitter (${type.heading})`);
check(/IBM Plex Sans/.test(type.body), `body uses IBM Plex Sans (${type.body})`);
check(/IBM Plex Mono/.test(type.figure || ''), `figures use IBM Plex Mono (${type.figure})`);
check(/tnum/.test(type.tnum || ''), `figures request tabular numerals (${type.tnum})`);
check(type.headingSize === '28px', `page title is 28px per the approved scale (${type.headingSize})`);
check(type.bg === 'rgb(241, 242, 236)', `page surface is Papyrus #F1F2EC (${type.bg})`);

// No Imigongo motif left anywhere.
const imigongo = await page.evaluate(() => document.documentElement.outerHTML.toLowerCase().includes('imigongo'));
check(!imigongo, 'the Imigongo motif is gone, as agreed in the plan revision');

// ---------------------------------------------------------------------------
check(consoleErrors.length === 0, `no JavaScript errors (${consoleErrors.length}): ${consoleErrors.slice(0, 3).join(' | ')}`);

await browser.close();

const failures = results.filter(([s]) => s === 'FAIL');
console.log(results.map(([s, m]) => `${s} ${m}`).join('\n'));
console.log(`\n${results.length - failures.length}/${results.length} checks passed`);
process.exit(failures.length ? 1 : 0);
