import type { JWTToken } from "@/types/jwttoken";

export function decode(token: string): JWTToken {
    try {
        const base64Url = token.split('.')[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        return JSON.parse(atob(base64));
    } catch (e) {
        throw new Error('Invalid token');
    }
}
