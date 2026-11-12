// resources/js/api-client.js
import axios from "axios";

// Create an axios instance with base URL and JSON headers
const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'https://api-zaku.abysoft.my.id/api/v1',
    headers: {
        "Content-Type": "application/json",
    },
    timeout: 15000,
    withCredentials: true, // Important: send cookies with cross-origin requests
});

// Auth endpoints that should NOT attach or validate existing tokens
const AUTH_ENDPOINTS = ['/v1/auth/login', '/v1/auth/register', '/v1/auth/forgot-password', '/v1/auth/verify-email', '/v1/auth/resend-verification', '/v1/auth/change-password', '/v1/auth/me', '/v1/auth/refresh', '/v1/auth/logout'];

// Request interceptor: no need to attach JWT manually (cookie sent automatically)
apiClient.interceptors.request.use(
    (config) => {
        // For auth endpoints, we don't need to do anything special
        // The cookie will be sent automatically with withCredentials: true
        return config;
    },
    (error) => Promise.reject(error),
);

// Response interceptor: handle 401 and network errors
apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            const isAuthEndpoint = AUTH_ENDPOINTS.some(ep => error.config?.url?.endsWith(ep));
            // Don't redirect on 401 from auth endpoints (invalid credentials is expected)
            if (!isAuthEndpoint) {
                // Clear user data and redirect to login
                localStorage.removeItem('user');
                sessionStorage.removeItem('user');
                if (window.location.pathname !== '/login') {
                    const separator = window.location.search ? '&' : '?';
                    window.location.href = `/login${separator}session=expired`;
                }
            }
            return Promise.reject(error);
        }
        return Promise.reject(error);
    },
);

export default apiClient;
