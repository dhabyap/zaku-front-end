// resources/js/auth.js

export const getToken = () => localStorage.getItem('access_token');
export const getRefreshToken = () => localStorage.getItem('refresh_token');

export const setToken = (access, refresh = null) => {
    localStorage.setItem('access_token', access);
    if (refresh) {
        localStorage.setItem('refresh_token', refresh);
    }
};

export const clearToken = () => {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    sessionStorage.removeItem('user');
    document.cookie = 'zaku_token=; path=/; max-age=0';

    if (window.location.pathname !== '/login') {
        const separator = window.location.search ? '&' : '?';
        window.location.href = `/login${separator}session=expired`;
    }
};

export const isTokenExpired = () => {
    const token = getToken();
    if (!token) return true;
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        // Exp is in seconds, add 60s buffer
        return payload.exp ? Date.now() >= (payload.exp * 1000) + 60000 : true;
    } catch {
        return true;
    }
};

export const isLoggedIn = () => {
    const token = getToken();
    return !!token && !isTokenExpired();
};

export const setUser = (user) => {
    if (!user) {
        sessionStorage.removeItem('user');
        return;
    }
    sessionStorage.setItem('user', JSON.stringify(user));
};

export const getUser = () => {
    try {
        const user = sessionStorage.getItem('user');
        return user && user !== 'undefined' ? JSON.parse(user) : null;
    } catch (e) {
        return null;
    }
};
