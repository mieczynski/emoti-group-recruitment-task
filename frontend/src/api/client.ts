const API_BASE = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8080';

type FetchOpts = RequestInit & { auth?: boolean };

const storage = {
    get accessToken() { return localStorage.getItem('accessToken'); },
    set accessToken(v: string | null) { v ? localStorage.setItem('accessToken', v) : localStorage.removeItem('accessToken'); },
    get refreshToken() { return localStorage.getItem('refreshToken'); },
    set refreshToken(v: string | null) { v ? localStorage.setItem('refreshToken', v) : localStorage.removeItem('refreshToken'); },
};

async function refreshToken(): Promise<boolean> {
    const rt = storage.refreshToken;
    if (!rt) return false;
    const res = await fetch(`${API_BASE}/api/auth/refresh`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ refresh_token: rt }),
    });
    if (!res.ok) return false;
    const data = await res.json();
    storage.accessToken = data.token;
    if (data.refresh_token) storage.refreshToken = data.refresh_token;
    return true;
}

export async function api(path: string, opts: FetchOpts = {}) {
    const headers: Record<string,string> = { 'Content-Type': 'application/json', ...(opts.headers as any) };
    if (opts.auth !== false && storage.accessToken) headers.Authorization = `Bearer ${storage.accessToken}`;

    let res = await fetch(`${API_BASE}${path}`, { ...opts, headers });

    if (res.status === 401 && opts.auth !== false) {
        const ok = await refreshToken();
        if (ok) {
            const headers2: Record<string,string> = { 'Content-Type': 'application/json', ...(opts.headers as any) };
            if (storage.accessToken) headers2.Authorization = `Bearer ${storage.accessToken}`;
            res = await fetch(`${API_BASE}${path}`, { ...opts, headers: headers2 });
        }
    }

    if (!res.ok) {
        let detail = '';
        try { detail = (await res.json()).detail ?? await res.text(); } catch { detail = await res.text(); }
        throw new Error(detail || `${res.status} ${res.statusText}`);
    }
    const txt = await res.text();
    return txt ? JSON.parse(txt) : null;
}

export const auth = {
    async login(email: string, password: string) {
        const res = await api('/api/auth/login', {
            method: 'POST',
            auth: false,
            body: JSON.stringify({ email, password }),
        });
        storage.accessToken = res.token;
        storage.refreshToken = res.refresh_token ?? storage.refreshToken;
    },
    async logout() {
        try { await api('/api/auth/logout', { method: 'POST' }); } catch { /* ignore */ }
        storage.accessToken = null;
        storage.refreshToken = null;
    },
    get accessToken() { return storage.accessToken; },
};
