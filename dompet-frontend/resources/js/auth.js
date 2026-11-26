// resources/js/auth.js
// Prefer the backend's HttpOnly cookie; keep a JS-readable fallback for browsers
// that omit cross-subdomain cookies from credentialed API requests.
export const getToken = () => localStorage.getItem('access_token') || sessionStorage.getItem('access_token');
export const getRefreshToken = () => null;

export const setToken = (access, refresh = null, remember = false) => {
    localStorage.removeItem('access_token');
    sessionStorage.removeItem('access_token');
    if (!access) return;
    const storage = remember ? localStorage : sessionStorage;
    storage.setItem('access_token', access);
};

export const clearToken = () => {
    localStorage.removeItem('user');
    sessionStorage.removeItem('user');
    localStorage.removeItem('access_token');
    sessionStorage.removeItem('access_token');
    // httpOnly cookie cleared by backend on logout call

    if (window.location.pathname !== '/login') {
        const separator = window.location.search ? '&' : '?';
        window.location.href = `/login${separator}session=expired`;
    }
};

// Check if user data exists (proxy for logged in status)
// Actual verification happens via API call to /auth/me
export const isLoggedIn = () => {
    return !!getUser();
};

export const setUser = (user, remember = false) => {
    localStorage.removeItem('user');
    sessionStorage.removeItem('user');
    if (!user) return;
    const storage = remember ? localStorage : sessionStorage;
    storage.setItem('user', JSON.stringify(user));
};

export const getUser = () => {
    try {
        const user = localStorage.getItem('user') || sessionStorage.getItem('user');
        return user && user !== 'undefined' ? JSON.parse(user) : null;
    } catch (e) {
        return null;
    }
};
