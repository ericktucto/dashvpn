import { axios } from "@/lib/axios"
import type { AxiosResponse } from "axios"

export function login(username: string, password: string) {
    return axios.post<
        { username: string, password: string },
        AxiosResponse<{ data: string }>
    >('/api/login', { username, password })
}
