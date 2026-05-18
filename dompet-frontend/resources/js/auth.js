// resources/js/auth.js

export const getToken = () => localStorage.getItem('access_token');
export const getRefreshToken = () => localStorage.getItem('refresh_token');

export const setToken = (access, refresh) => {
    localStorage.setItem('access_token', access);
    if (refresh) {
        localStorage.setItem('refresh_token', refresh);
    }
};

export const clearToken = () => {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    sessionStorage.removeItem('user');

    // Only redirect if not already on login page
    if (window.location.pathname !== '/login') {
        // Show session expired message via URL param
        const separator = window.location.search ? '&' : '?';
        window.location.href = `/login${separator}session=expired`;
    }
};

export const isLoggedIn = () => {
    const token = getToken();
    // Basic check for presence of token
    // In a real app, you might decode the JWT to check expiration
    return !!token;
};

export const setUser = (user) => {
    sessionStorage.setItem('user', JSON.stringify(user));
};

export const getUser = () => {
    const user = sessionStorage.getItem('user');
    return user ? JSON.parse(user) : null;
};
