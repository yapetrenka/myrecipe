export async function fetchApi() {
    const isLocalHostNames = ['localhost', '127.0.0.1', '::1'];
    const isLocal = isLocalHostNames.includes(window.location.hostname) || process.env.NODE_ENV === 'development';
    const url = isLocal ? '/api/local.json' : '/api.php';

    const res = await fetch(url);
    const text = await res.text();
    const ct = (res.headers.get('Content-Type') || '').toLowerCase();

    if (!res.ok) throw new Error('HTTP ' + res.status);
    if (!ct.includes('application/json')) throw new Error('Unexpected response (not JSON): ' + text.slice(0, 200));
    try {
        return JSON.parse(text);
    } catch (e) {
        throw new Error('Invalid JSON: ' + e.message);
    }
}

export async function getPages() {
    const data = await fetchApi();
    return Array.isArray(data.pages) ? data.pages : [];
}

export async function getCategories() {
    const data = await fetchApi();
    return Array.isArray(data.recipe_category) ? data.recipe_category : [];
}

export async function getRecipes() {
    const data = await fetchApi();
    return Array.isArray(data.recipe) ? data.recipe : [];
}