import axios from "axios";

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8686',
    headers: {
        Authorization: `Bearer ${localStorage.getItem('token') ?? ''}`,
        "Content-Type": "application/json",
    },
});

export { instance as axios }
