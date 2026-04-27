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
    ip: string
    address: string
    listen_port: number
    dns: string
    'interface': string
    post_up: string[]
    post_down: string[]
}
export function postServer(data: PostServer) {
    return axios.post<
        Server,
        AxiosResponse<{ data: Server }>,
        PostServer
    >('/api/wireguard/server', data)
}

type GetRecommended = {
    address: string
    listen_port: number
    dns: string
    post_up: string[]
    post_down: string[]
    'interface': string
}
export function getRecommended() {
    return axios.get<
        {},
        AxiosResponse<{ data: GetRecommended }>
    >('/api/wireguard/server/recommended')
}

export function getInterfaces() {
    return axios.get<
        {},
        AxiosResponse<{ data: string[] }>
    >('/api/system/interfaces')
}
