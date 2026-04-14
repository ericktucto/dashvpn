import { axios } from "@/lib/axios"
import type { AxiosResponse } from "axios"

export interface Server {
    address: string;
    ip: string;
    port: number;
    dns: string;
    public_key: string;
}

export function getServer() {
    return axios.get<
        {},
        AxiosResponse<{ data: Server }>
    >('/api/wireguard/server')
}
export type PostServer = {
    ip: string, address: string, listen_port: number, dns: string
}
export function postServer(data: PostServer) {
    return axios.post<
        Server,
        AxiosResponse<{ data: Server }>,
        PostServer
    >('/api/wireguard/server', data)
}
