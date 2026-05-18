// resources/js/api-client.js
import axios from 'axios';
import { getToken, setToken, clearToken } from './auth';

// Create an axios instance with base URL and JSON headers
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to attach JWT token if present
apiClient.interceptors.request.use(
  (config) => {
    const token = getToken();
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Helper to refresh token using refresh token stored in localStorage
async function refreshAccessToken() {
  const refreshToken = localStorage.getItem('refresh_token');
  if (!refreshToken) {
    return null;
  }
  try {
    const response = await axios.post(
      `${import.meta.env.VITE_API_BASE_URL || '/api'}/auth/refresh`,
      { refresh_token: refreshToken }
    );
    const { access_token, refresh_token } = response.data;
    setToken(access_token, refresh_token);
    return access_token;
  } catch (err) {
    clearToken();
    return null;
  }
}

// Response interceptor to handle 401 errors and attempt token refresh
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      const newAccessToken = await refreshAccessToken();

      if (newAccessToken) {
        originalRequest.headers['Authorization'] = `Bearer ${newAccessToken}`;
        return apiClient(originalRequest);
      }

      return Promise.reject(error);
    }

    if (error.response && error.response.status === 401 && originalRequest._retry) {
      clearToken();
      return Promise.reject(error);
    }

    return Promise.reject(error);
  }
);

export default apiClient;
