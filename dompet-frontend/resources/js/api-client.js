// resources/js/api-client.js
import axios from "axios";

// Create an axios instance with base URL and JSON headers
const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || '',
    headers: {
        "Content-Type": "application/json",
    },
    timeout: 15000,
    withCredentials: true, // Important: send httpOnly cookies with cross-origin requests
});

// Auth endpoints that should NOT redirect on 401 (invalid credentials is expected)
const AUTH_ENDPOINTS = ['/v1/auth/login', '/v1/auth/register', '/v1/auth/forgot-password', '/v1/auth/verify-email', '/v1/auth/resend-verification', '/v1/auth/change-password'];

// Response interceptor: handle 401 and network errors
apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        const status = error.response?.status;
        const isAuthEndpoint = AUTH_ENDPOINTS.some(ep => error.config?.url?.endsWith(ep));
        const body = error.response?.data;
        const apiMessage = [body?.message, body?.error, body?.errors?.token]
            .flat()
            .filter((value) => typeof value === 'string')
            .join(' ');
        const invalidSession = status === 401
            || (status >= 400
                && /token|session|sesi/i.test(apiMessage)
                && /expired|invalid|not registered|unregistered|revoked|invalidated|kedaluwarsa|tidak valid|tidak terdaftar|tidak berakses/i.test(apiMessage));

        // Login/register errors are user-correctable; don't treat those as expired sessions.
        if (invalidSession && !isAuthEndpoint) {
            localStorage.removeItem('user');
            sessionStorage.removeItem('user');
            if (window.location.pathname !== '/login' && !window.__sessionRedirecting) {
                window.__sessionRedirecting = true;
                window.location.replace('/login?session=expired');
            }
        }

        if (status === 401) {
            return Promise.reject(error);
        }
        return Promise.reject(error);
    },
);

export default apiClient;
