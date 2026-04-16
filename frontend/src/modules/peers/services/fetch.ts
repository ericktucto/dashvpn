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

export type PutPeer = {
    name: string
    address: string
}
export function postPeer(name: string) {
    return axios.post<
        Peer,
        AxiosResponse<{ data: Peer }>,
        PostPeer
    >('/api/wireguard/peers', { name })
}

export function putPeer(slug: string, data: PutPeer) {
    return axios.put<
        Peer,
        AxiosResponse<{ data: Peer, message: string }>,
        PutPeer
    >(`/api/wireguard/peers/${slug}`, data)
}

export function getConfigPeer(slug: string) {
    return axios.get(`/api/wireguard/peers/${slug}/config`)
}

export function getNextAddress() {
    return axios.get<
        {},
        AxiosResponse<{ data: { address: string } }>
    >('/api/wireguard/peers/next-address')
}

export interface GetLink {
    url: string;
    otp: string;
}
export function getLink(slug: string) {
    return axios.get<
        {},
        AxiosResponse<{ data: GetLink }>
    >(`/api/wireguard/peers/${slug}/share`)
}
