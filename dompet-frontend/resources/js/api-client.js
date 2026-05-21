// resources/js/api-client.js
import axios from "axios";
import { getToken, setToken, clearToken } from "./auth";

// Create an axios instance with base URL and JSON headers
const apiClient = axios.create({
    baseURL: "https://api-zaku.abysoft.my.id/api",
    headers: {
        "Content-Type": "application/json",
    },
});

// Request interceptor to attach JWT token if present
apiClient.interceptors.request.use(
    (config) => {
        const token = getToken();
        if (token) {
            config.headers["Authorization"] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error),
);

// Response interceptor to handle 401 errors - clear token and redirect to login
apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (error.response && error.response.status === 401) {
            clearToken();
            return Promise.reject(error);
        }
        return Promise.reject(error);
    },
);

export default apiClient;
