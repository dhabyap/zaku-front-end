// resources/js/auth.js
// Token is now stored in httpOnly cookie (set by backend), not accessible via JS.
// We only store user data in localStorage/sessionStorage.

export const getToken = () => null; // Token in httpOnly cookie, not readable by JS
export const getRefreshToken = () => null;

export const setToken = (access, refresh = null, remember = false) => {
    // No-op: token is in httpOnly cookie managed by backend
    // We only store user data
};

export const clearToken = () => {
    localStorage.removeItem('user');
    sessionStorage.removeItem('user');
    // Cookie cleared by backend on logout endpoint

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
