import axios from "axios";

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? 'http://10.8.0.2:8686',
    headers: {
        "Content-Type": "application/json",
    },
});

instance.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    } else {
        delete config.headers.Authorization;
    }

    return config;
});

export { instance as axios }
