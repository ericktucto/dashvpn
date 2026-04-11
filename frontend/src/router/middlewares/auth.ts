import type { To } from "../types";
import { decode } from "@/lib/jwt";

export async function isAuthenticated() {
    const token = localStorage.getItem('token');

    if (!token) return false;

    try {
        const decoded = decode(token);
        return new Date(decoded.exp * 1000) > new Date();
    } catch {
        return false;
    }
}

const guest = [
    'auth@login',
    'auth@register',
    'auth@forgot-password',
]

export default async function (to: To) {
    console.log("DEBUG", to.name);
    const isAuth = await isAuthenticated();

    // No autenticado → bloquear privadas
    if (!isAuth && !guest.includes(to.name as string)) {
        console.log("DEBUG", 'login');
        return { name: 'auth@login' }
    }

    // Autenticado → bloquear páginas de auth
    if (isAuth && guest.includes(to.name as string)) {
        console.log("DEBUG", 'dashboard');
        return { name: 'dashboard' }
    }
}
