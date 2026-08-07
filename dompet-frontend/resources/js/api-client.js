// resources/js/api-client.js
import axios from "axios";
import { getToken, isTokenExpired, clearToken } from "./auth";

// Create an axios instance with base URL and JSON headers
const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'https://api-zaku.abysoft.my.id/api/v1',
    headers: {
        "Content-Type": "application/json",
    },
    timeout: 15000,
});

// Auth endpoints that should NOT attach or validate existing tokens
const AUTH_ENDPOINTS = ['/v1/auth/login', '/v1/auth/register', '/v1/auth/forgot-password', '/v1/auth/verify-email', '/v1/auth/resend-verification'];

// Request interceptor: attach JWT, check expiry before sending
apiClient.interceptors.request.use(
    (config) => {
        // Skip token logic for auth endpoints (login, register, etc.)
        const isAuthEndpoint = AUTH_ENDPOINTS.some(ep => config.url?.endsWith(ep));
        if (isAuthEndpoint) return config;

        const token = getToken();
        if (token) {
            if (isTokenExpired()) {
                clearToken();
                return Promise.reject(new axios.Cancel('Session expired'));
            }
            config.headers["Authorization"] = `Bearer ${token}`;
        }
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
            // Don't clear token on 401 from auth endpoints (invalid credentials is expected)
            if (!isAuthEndpoint) {
                clearToken();
            }
            return Promise.reject(error);
        }
        return Promise.reject(error);
    },
);

export default apiClient;
