import { toast } from "vue-sonner"
import { putPeer, type Peer, type PutPeer } from "../services/fetch"
import { isAxiosError } from "axios"

export async function handleUpdate(peer: Peer, newData: PutPeer) {
    const validIp = /^\d+\.\d+\.\d+\.\d+$/.test(newData.address)
    if (!validIp) {
        toast.error('Address invalid')
    }
    try {
        const response = await putPeer(peer.slug, newData)
        toast.info(response.data.message)
        return response.data.data
    } catch (error) {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
        return null
    }
}
