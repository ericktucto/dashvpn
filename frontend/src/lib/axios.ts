import axios from "axios";

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? 'http://10.8.0.2:8686',
    headers: {
        Authorization: `Bearer ${localStorage.getItem('token') ?? ''}`,
        "Content-Type": "application/json",
    },
});

export { instance as axios }
