import { axios } from "@/lib/axios"
import type { AxiosResponse } from "axios"

export interface OnlyMessage {
    message: string
}

export interface Peer {
    name: string;
    address: string;
    publicKey: string;
    slug: string;

}

export type ResponseGetPeers = Peer[]
export function getPeers() {
    return axios.get<
        {},
        AxiosResponse<{ data: ResponseGetPeers }>
    >('/api/wireguard/peers')
}
export function deletePeer(slug: string) {
    return axios.delete<
        {},
        AxiosResponse<OnlyMessage>
    >(`/api/wireguard/peers/${slug}`)
}
export type PostPeer = {
    name: string
}
export function postPeer(name: string) {
    return axios.post<
        Peer,
        AxiosResponse<{ data: Peer }>,
        PostPeer
    >('/api/wireguard/peers', { name })
}

