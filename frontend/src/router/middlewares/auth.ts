import { decodeJWT } from "@/lib/utils";
import type { From, To } from "../types";

export async function isAuthenticated() {
    const token = localStorage.getItem('token') ?? '';
    try {
        const decoded = decodeJWT(token);
        return new Date(decoded.exp * 1000) > new Date();
    } catch (e) {
    }
    return false
}

const guest = [
    'auth@login',
    'auth@register',
    'auth@forgot-password',
]

export default async function (to: To, _: From) {
    const check = await isAuthenticated();
    if (check && guest.includes(to.name as string)) {
        return { name: 'dashboard' }
    }
    if (!check && !guest.includes(to.name as string)) {
        return { name: 'auth@login' }
    }
}
