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

// Request interceptor: attach JWT, check expiry before sending
apiClient.interceptors.request.use(
    (config) => {
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

// Response interceptor: handle401 and network errors
apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response?.status === 401) {
            clearToken();
            return Promise.reject(error);
        }
        return Promise.reject(error);
    },
);

export default apiClient;
