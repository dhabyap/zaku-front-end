// resources/js/auth.js

export const getToken = () => localStorage.getItem('access_token');
export const setToken = (access, refresh) => {
    localStorage.setItem('access_token', access);
    localStorage.setItem('refresh_token', refresh);
};
export const clearToken = () => {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
};
export const isLoggedIn = () => !!getToken();
export const getUser = () => JSON.parse(sessionStorage.getItem('user'));
