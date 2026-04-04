/**
 * AgriFlow – Frontend Application
 * Connects to the Symfony REST API at http://127.0.0.1:8000
 */

// API base URL – override via <meta name="api-base" content="..."> in index.html.
// Only http/https URLs are accepted to prevent open redirects.
const metaApiBase = document.querySelector('meta[name="api-base"]');
const rawApiBase = (metaApiBase && metaApiBase.content) ? metaApiBase.content : '';
const API_BASE = /^https?:\/\/[^/]/.test(rawApiBase) ? rawApiBase.replace(/\/$/, '') : 'http://127.0.0.1:8000';

// ── Navigation ────────────────────────────────────────────

const navItems = document.querySelectorAll('.nav-item');
const pageTitle = document.getElementById('page-title');

const sectionTitles = {
    dashboard: 'Tableau de bord',
    irrigation: "Plans d'Irrigation",
    diagnostic: 'Diagnostics',
    produits: 'Produits Phytosanitaires',
};

navItems.forEach((item) => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        const section = item.dataset.section;
        activateSection(section);
    });
});

function activateSection(section) {
    // Update nav highlight
    navItems.forEach((i) => i.classList.remove('active'));
    const activeNav = document.querySelector(`.nav-item[data-section="${section}"]`);
    if (activeNav) activeNav.classList.add('active');

    // Switch visible section
    document.querySelectorAll('.section').forEach((s) => {
        s.classList.remove('active');
        s.classList.add('hidden');
    });
    const target = document.getElementById(`section-${section}`);
    if (target) {
        target.classList.remove('hidden');
        target.classList.add('active');
    }

    // Update page title
    pageTitle.textContent = sectionTitles[section] ?? section;

    // Load data for the section
    loadSection(section);
}

// ── Data loading ──────────────────────────────────────────

async function apiFetch(path) {
    const response = await fetch(`${API_BASE}${path}`);
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }
    return response.json();
}

function loadSection(section) {
    switch (section) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'irrigation':
            loadIrrigation();
            break;
        case 'diagnostic':
            loadDiagnostics();
            break;
        case 'produits':
            loadProduits();
            break;
    }
}

// ── Dashboard ─────────────────────────────────────────────

async function loadDashboard() {
    const errorBanner = document.getElementById('api-error');
    try {
        const result = await apiFetch('/api/dashboard/statistics');
        if (result.success && result.data) {
            document.getElementById('stat-parcelles').textContent = result.data.parcelles ?? '–';
            document.getElementById('stat-cultures').textContent = result.data.cultures ?? '–';
            document.getElementById('stat-water').textContent = result.data.waterPerWeek ?? '–';
        }
        errorBanner.classList.add('hidden');
    } catch (err) {
        console.error('Dashboard API error:', err);
        errorBanner.classList.remove('hidden');
    }
}

// ── Irrigation plans ──────────────────────────────────────

async function loadIrrigation() {
    const container = document.getElementById('irrigation-list');
    container.innerHTML = '<p class="loading">Chargement…</p>';
    try {
        const result = await apiFetch('/api/irrigation-plans');
        if (result.success) {
            renderList(container, result.data, (item) => `
                <div class="data-card">
                    <div>
                        <div class="data-card-title">${escapeHtml(item.name)}</div>
                        <div class="data-card-meta">Culture : ${escapeHtml(item.culture)}</div>
                    </div>
                    <span class="badge">${escapeHtml(String(item.waterNeeded))} L</span>
                </div>
            `);
        }
    } catch (err) {
        console.error('Irrigation API error:', err);
        container.innerHTML = '<p class="empty-state">Impossible de charger les plans.</p>';
    }
}

// ── Diagnostics ───────────────────────────────────────────

async function loadDiagnostics() {
    const container = document.getElementById('diagnostic-list');
    container.innerHTML = '<p class="loading">Chargement…</p>';
    try {
        const result = await apiFetch('/api/diagnostics');
        if (result.success) {
            renderList(container, result.data, (item) => `
                <div class="data-card">
                    <div>
                        <div class="data-card-title">${escapeHtml(item.title ?? 'Diagnostic')}</div>
                        <div class="data-card-meta">${escapeHtml(item.description ?? '')}</div>
                    </div>
                </div>
            `);
        }
    } catch (err) {
        console.error('Diagnostic API error:', err);
        container.innerHTML = '<p class="empty-state">Impossible de charger les diagnostics.</p>';
    }
}

// ── Produits ──────────────────────────────────────────────

async function loadProduits() {
    const container = document.getElementById('produits-list');
    container.innerHTML = '<p class="loading">Chargement…</p>';
    try {
        const result = await apiFetch('/api/produits');
        if (result.success) {
            renderList(container, result.data, (item) => `
                <div class="data-card">
                    <div>
                        <div class="data-card-title">${escapeHtml(item.name ?? 'Produit')}</div>
                        <div class="data-card-meta">${escapeHtml(item.category ?? '')}</div>
                    </div>
                </div>
            `);
        }
    } catch (err) {
        console.error('Produits API error:', err);
        container.innerHTML = '<p class="empty-state">Impossible de charger les produits.</p>';
    }
}

// ── Helpers ───────────────────────────────────────────────

function renderList(container, items, templateFn) {
    if (!Array.isArray(items) || items.length === 0) {
        container.innerHTML = '<p class="empty-state">Aucune donnée disponible.</p>';
        return;
    }
    container.innerHTML = items.map(templateFn).join('');
}

function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// ── Bootstrap ─────────────────────────────────────────────

activateSection('dashboard');
