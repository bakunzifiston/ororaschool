/**
 * Phase F2 browser checks — unauthenticated pages.
 *
 *   php artisan serve --port=8123
 *   npm install --no-save puppeteer-core
 *   node .verify/f2-check.mjs
 */
import puppeteer from 'puppeteer-core';
import { mkdirSync } from 'node:fs';

const BASE = 'http://127.0.0.1:8123';
const OUT = '.verify/shots';
mkdirSync(OUT, { recursive: true });

const results = [];
const check = (cond, m) => results.push([cond ? 'ok  ' : 'FAIL', m]);

const browser = await puppeteer.launch({
    executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    headless: 'new',
    args: ['--no-sandbox', '--force-color-profile=srgb', '--font-render-hinting=none'],
});

const page = await browser.newPage();
const consoleErrors = [];
page.on('pageerror', (e) => consoleErrors.push(String(e)));
page.on('console', (m) => { if (m.type() === 'error') consoleErrors.push(m.text()); });

const go = async (path, width = 1280, height = 1000) => {
    await page.setViewport({ width, height, deviceScaleFactor: 2 });
    await page.goto(BASE + path, { waitUntil: 'networkidle0' });
    await new Promise((r) => setTimeout(r, 300));
};
const overflow = () => page.evaluate(() =>
    document.documentElement.scrollWidth - document.documentElement.clientWidth);

const pages = [
    ['/login', 'login', 'Sign in'],
    ['/register', 'register', 'Get your Orora School account'],
    ['/forgot-password', 'forgot-password', 'Reset your password'],
    ['/reset-password/fixture-token-9f2c', 'reset-password', 'Set a new password'],
    ['/verify-email', 'verify-email', 'Confirm your email address'],
    ['/verify-email/confirmed', 'verified', 'Email confirmed'],
];

// ---------------------------------------------------------------------------
// 1. Every page: guest layout, right heading, no app chrome, no overflow
// ---------------------------------------------------------------------------
for (const [path, shot, heading] of pages) {
    await go(path);

    const info = await page.evaluate(() => ({
        experience: document.documentElement.dataset.experience,
        h1: document.querySelector('h1')?.textContent.trim(),
        h1Count: document.querySelectorAll('h1').length,
        aside: !!document.querySelector('aside'),
        accent: getComputedStyle(document.documentElement).getPropertyValue('--xp-accent-500').trim(),
        // Every input must be reachable by its label.
        unlabelled: [...document.querySelectorAll('input:not([type=hidden]), select')]
            .filter((el) => !el.labels?.length && !el.getAttribute('aria-label')).length,
        inputs: document.querySelectorAll('input:not([type=hidden]), select').length,
        // Touch target height for the audience: phones, often outdoors.
        // Form controls get a generous 44px; any other clickable target must
        // still clear the 24px WCAG 2.5.8 minimum. Links set inside a paragraph
        // are genuinely inline text and exempt.
        shortControls: [...document.querySelectorAll('input:not([type=hidden]):not([type=checkbox]), select')]
            .filter((el) => el.getBoundingClientRect().height < 40)
            .map((el) => `${el.tagName}:${Math.round(el.getBoundingClientRect().height)}px`),
        // The primary action of each form is full width by convention here.
        smallPrimaries: [...document.querySelectorAll('button[type=submit].w-full')]
            .filter((el) => el.getBoundingClientRect().height < 44)
            .map((el) => `${el.innerText.trim().slice(0, 18)}:${Math.round(el.getBoundingClientRect().height)}px`),
        smallTargets: [...document.querySelectorAll('a[href], button')]
            .filter((el) => el.offsetParent !== null && !el.closest('p'))
            .filter((el) => el.getBoundingClientRect().height < 24)
            .map((el) => `${el.innerText.trim().slice(0, 18) || el.tagName}:${Math.round(el.getBoundingClientRect().height)}px`),
    }));

    check(info.experience === 'guest', `${path} renders in the guest layout`);
    check(info.h1 === heading, `${path} heading is "${heading}" (got "${info.h1}")`);
    check(info.h1Count === 1, `${path} has exactly one h1 (${info.h1Count})`);
    check(!info.aside, `${path} has no sidebar`);
    check(info.accent === '#3e6b34', `${path} uses the house accent, not an experience accent`);
    check(info.unlabelled === 0, `${path}: all ${info.inputs} inputs are labelled (${info.unlabelled} unlabelled)`);
    check(info.shortControls.length === 0,
        `${path}: every data-entry control is at least 40px tall${info.shortControls.length ? ` — ${info.shortControls.join(', ')}` : ''}`);
    check(info.smallPrimaries.length === 0,
        `${path}: every primary action is at least 44px tall${info.smallPrimaries.length ? ` — ${info.smallPrimaries.join(', ')}` : ''}`);
    check(info.smallTargets.length === 0,
        `${path}: every standalone target clears 24px${info.smallTargets.length ? ` — ${info.smallTargets.join(', ')}` : ''}`);

    await page.screenshot({ path: `${OUT}/f2-${shot}-desktop.png`, fullPage: true });

    for (const w of [1280, 820, 375]) {
        await go(path, w, 1000);
        const over = await overflow();
        check(over <= 0, `${path}: no horizontal overflow at ${w}px (${over}px)`);
    }
    await page.screenshot({ path: `${OUT}/f2-${shot}-mobile.png`, fullPage: true });
}

// ---------------------------------------------------------------------------
// 2. Not a Breeze default: check the treatment that makes it Orora School
// ---------------------------------------------------------------------------
await go('/login');
const treatment = await page.evaluate(() => {
    const sheet = document.querySelector('main section');
    const band = sheet.querySelector('header');
    const h1 = sheet.querySelector('h1');
    const masthead = document.querySelector('.on-basalt');
    const body = document.body;
    return {
        radius: getComputedStyle(sheet).borderTopLeftRadius,
        shadow: getComputedStyle(sheet).boxShadow,
        bandRule: getComputedStyle(band).borderBottomWidth,
        bandBg: getComputedStyle(band).backgroundColor,
        sheetBg: getComputedStyle(sheet).backgroundColor,
        pageBg: getComputedStyle(body).backgroundColor,
        headingFont: getComputedStyle(h1).fontFamily,
        mastheadBg: masthead ? getComputedStyle(masthead).backgroundColor : null,
        gradients: [...document.querySelectorAll('*')]
            .filter((el) => getComputedStyle(el).backgroundImage.includes('gradient')).length,
    };
});
check(treatment.radius === '3px', `form sheet uses a 3px radius, not a soft rounded card (${treatment.radius})`);
check(treatment.shadow === 'none', `form sheet has no drop shadow (${treatment.shadow})`);
check(parseFloat(treatment.bandRule) === 2, `sheet header carries the heavier 2px ledger rule (${treatment.bandRule})`);
check(treatment.sheetBg === 'rgb(251, 252, 249)', `sheet is Chalk on a Papyrus page (${treatment.sheetBg})`);
check(treatment.pageBg === 'rgb(241, 242, 236)', `page surface is Papyrus (${treatment.pageBg})`);
check(/Bitter/.test(treatment.headingFont), `form title is set in Bitter (${treatment.headingFont})`);
check(treatment.mastheadBg === 'rgb(17, 21, 18)', `masthead band is Basalt (${treatment.mastheadBg})`);
check(treatment.gradients === 0, `no gradient decoration anywhere (${treatment.gradients} found)`);

// ---------------------------------------------------------------------------
// 3. The temporary preview control is unmistakable and works
// ---------------------------------------------------------------------------
const dev = await page.evaluate(() => {
    const select = document.querySelector('select[name="preview_as"]');
    const strip = select?.closest('div');
    return {
        found: !!select,
        options: [...select.options].map((o) => o.value),
        dashed: getComputedStyle(strip).borderStyle,
        text: strip.innerText,
        insideForm: !!select.closest('form[action$="/login"]'),
    };
});
check(dev.found, 'preview selector is present on the sign-in page');
check(dev.dashed === 'dashed', `preview strip is dashed so it cannot read as real UI (${dev.dashed})`);
check(/Temporary/.test(dev.text), 'preview strip is labelled Temporary');
check(dev.insideForm, 'preview selector posts with the sign-in form');
check(JSON.stringify(dev.options) === JSON.stringify(['super-admin', 'platform-workspace', 'learner']),
    `preview offers the three shells (${dev.options.join(', ')})`);

for (const [value, expected] of [
    ['super-admin', '/admin'],
    ['platform-workspace', '/workspace/gemura'],
    ['learner', '/learn'],
]) {
    await go('/login');
    await page.select('select[name="preview_as"]', value);
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle0' }),
        page.click('form[action$="/login"] button[type="submit"]'),
    ]);
    const landed = new URL(page.url()).pathname;
    check(landed === expected, `previewing ${value} opens ${expected} (landed ${landed})`);

    const flash = await page.evaluate(() => document.querySelector('[role="status"]')?.innerText || '');
    check(/No credentials were checked/.test(flash), `${value}: the shell says plainly that nothing was checked`);
    if (value === 'super-admin') {
        await page.screenshot({ path: `${OUT}/f2-preview-landing.png` });
    }
}

// ---------------------------------------------------------------------------
// 4. Password reveal toggle
// ---------------------------------------------------------------------------
await go('/login');
const reveal = await page.evaluate(() => {
    const input = document.querySelector('#field-password');
    const btn = input.parentElement.querySelector('button');
    const before = input.type;
    btn.click();
    return { before, label: btn.getAttribute('aria-label'), pressed: btn.getAttribute('aria-pressed') };
});
await new Promise((r) => setTimeout(r, 150));
const revealed = await page.evaluate(() => ({
    type: document.querySelector('#field-password').type,
    label: document.querySelector('#field-password').parentElement.querySelector('button').getAttribute('aria-label'),
}));
check(reveal.before === 'password', 'password starts masked');
check(revealed.type === 'text', `reveal toggle unmasks the password (now ${revealed.type})`);
check(revealed.label === 'Hide password', `toggle relabels itself for screen readers (${revealed.label})`);

// ---------------------------------------------------------------------------
// 5. Flows connect: forgot -> flash, resend -> flash, sign-out -> login
// ---------------------------------------------------------------------------
await go('/forgot-password');
await page.type('#field-email', 'nobody@example.rw');
await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('form button[type="submit"]'),
]);
const resetFlash = await page.evaluate(() => document.querySelector('[role="status"]')?.innerText || '');
check(/^If that address/.test(resetFlash), `reset copy does not disclose whether the account exists ("${resetFlash.slice(0, 40)}…")`);
await page.screenshot({ path: `${OUT}/f2-forgot-flash.png` });

await go('/verify-email');
await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('form[action$="/verify-email"] button[type="submit"]'),
]);
check(/p\.bizimana@umuhinzi\.rw/.test(await page.evaluate(() => document.body.innerText)),
    'resending names the address the link went to');

await go('/admin');
await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('header form[action$="/sign-out"] button'),
]);
check(new URL(page.url()).pathname === '/login', `signing out lands on the sign-in page (${new URL(page.url()).pathname})`);
check(/Signed out/.test(await page.evaluate(() => document.querySelector('[role="status"]')?.innerText || '')),
    'sign-out reports back on the sign-in page');

// ---------------------------------------------------------------------------
// 6. Registration leads with linking
// ---------------------------------------------------------------------------
await go('/register');
const reg = await page.evaluate(() => {
    const linkForm = document.querySelector('form[action$="/register/link"]');
    const directForm = document.querySelector('form[action$="/register"]');
    return {
        linkButtons: [...linkForm.querySelectorAll('button[name="platform"]')].map((b) => b.value),
        linkingFirst: linkForm.compareDocumentPosition(directForm) & Node.DOCUMENT_POSITION_FOLLOWING ? true : false,
        districts: document.querySelectorAll('#field-district option').length,
    };
});
check(JSON.stringify(reg.linkButtons) === JSON.stringify(['ororafarm', 'gemura', 'buchapro', 'feedgrid']),
    `all four platforms offered for linking (${reg.linkButtons.join(', ')})`);
check(reg.linkingFirst, 'platform linking comes before the direct sign-up form');
check(reg.districts === 16, `district list is populated from the fixture (${reg.districts} options incl. placeholder)`);

await Promise.all([
    page.waitForNavigation({ waitUntil: 'networkidle0' }),
    page.click('button[name="platform"][value="gemura"]'),
]);
check(/Gemura/.test(await page.evaluate(() => document.querySelector('[role="status"]')?.innerText || '')),
    'linking with Gemura reports back naming that platform');

// ---------------------------------------------------------------------------
// 7. Focus rings on every guest control type
// ---------------------------------------------------------------------------
await go('/register');
const rings = await page.evaluate(() => {
    const targets = [
        ['text input', '#field-name'],
        ['select', '#field-district'],
        ['password input', '#field-password'],
        ['reveal toggle', '#field-password + button, .relative button'],
        ['platform link button', 'button[name="platform"]'],
        ['submit button', 'form[action$="/register"] button[type="submit"]'],
        ['sheet footer link', 'footer a'],
    ];
    return targets.map(([label, sel]) => {
        const el = [...document.querySelectorAll(sel)].find((e) => e.offsetParent !== null);
        if (!el) return { label, found: false };
        el.focus();
        const s = getComputedStyle(el);
        return {
            label, found: true,
            visible: (parseFloat(s.outlineWidth) || 0) >= 1 && s.outlineStyle !== 'none',
            outline: `${s.outlineStyle} ${s.outlineWidth} ${s.outlineColor}`,
        };
    });
});
for (const r of rings) {
    check(r.found && r.visible, `focus ring on ${r.label}${r.found ? ` (${r.outline})` : ' — not found'}`);
}

// ---------------------------------------------------------------------------
// 8. Motion stays where it was agreed
// ---------------------------------------------------------------------------
await go('/login');
const animated = await page.evaluate(() => [...document.querySelectorAll('main *')]
    .filter((el) => {
        const s = getComputedStyle(el);
        // A transition declared with a zero duration is Chrome's initial
        // computed value, not motion.
        const duration = parseFloat(s.transitionDuration) || 0;
        return s.animationName !== 'none' || (duration > 0 && /opacity|transform|all/.test(s.transitionProperty));
    }).length);
check(animated === 0, `no entrance animation on the form (${animated} animated elements)`);

check(consoleErrors.length === 0, `no JavaScript errors (${consoleErrors.length}): ${consoleErrors.slice(0, 3).join(' | ')}`);

await browser.close();
const failures = results.filter(([s]) => s === 'FAIL');
console.log(results.map(([s, m]) => `${s} ${m}`).join('\n'));
console.log(`\n${results.length - failures.length}/${results.length} checks passed`);
process.exit(failures.length ? 1 : 0);
