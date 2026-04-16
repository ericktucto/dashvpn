<script setup lang="ts">
import {
    Table,
    TableBody,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import ContainerCenter from '@/layouts/ContainerCenter.vue';
import { onMounted, ref } from 'vue';
import { deletePeer, getPeers, putPeer, type Peer, type PutPeer } from '../services/fetch';
import { toast } from 'vue-sonner';
import RowSave from '../components/organisms/RowSave.vue';
import { isAxiosError } from 'axios';
import RowPeerMobile from '../components/organisms/RowPeerMobile.vue';
import TableRowPeer from '../components/organisms/TableRowPeer.vue';
import DialogSave from '../components/organisms/DialogSave.vue';

const peers = ref<Peer[]>([])
const showRowSave = ref(false)

onMounted(() => {
    getPeers().then(res => peers.value = res.data.data)
})

async function handleUpdate(peer: Peer, newData: PutPeer, index: number) {
    const validIp = /^\d+\.\d+\.\d+\.\d+$/.test(newData.address)
    if (!validIp) {
        toast.error('Address invalid')
    }
    try {
        const response = await putPeer(peer.slug, newData)
        peers.value[index] = response.data.data
        toast.info(response.data.message)
    } catch (error) {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
    }
}
function handleDelete(peer: Peer, index: number) {
    deletePeer(peer.slug).then(res => {
        toast.info(res.data.message)
        peers.value.splice(index, 1)
    })
}
function handleSave(peer: Peer) {
    peers.value.push(peer)
}
</script>

<template>
    <div class="w-screen mt-[69px] p-8 grid md:hidden gap-4 mb-16">
        <RowPeerMobile v-for="(peer, index) in peers" :key="`${peer.slug}-rowpeermobile`" :peer="peer"
            @delete="handleDelete(peer, index)" @update="handleUpdate(peer, $event, index)" />
        <DialogSave @save="handleSave" />
    </div>
    <ContainerCenter class="hidden md:grid">
        <div class="min-w-200 border rounded-sm">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="pl-8 w-[240px]">
                            NOMBRE DEL PEER
                        </TableHead>
                        <TableHead>IP ADDRESS</TableHead>
                        <TableHead>ACTIVO</TableHead>
                        <TableHead class="text-right pr-8 w-[300px]">
                            ACCIONES
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRowPeer v-for="(peer, index) in peers" :key="`${peer.slug}-rowpeer`" :peer="peer"
                        @delete="handleDelete(peer, index)" @update="handleUpdate(peer, $event, index)" />
                </TableBody>
                <TableFooter>
                    <RowSave @cancel="showRowSave = false" @save="handleSave" />
                </TableFooter>
            </Table>
        </div>
    </ContainerCenter>
</template>
