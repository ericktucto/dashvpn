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
import { deletePeer, getPeers, type Peer } from '../services/fetch';
import { toast } from 'vue-sonner';
import RowSave from '../components/organisms/RowSave.vue';
import { isAxiosError } from 'axios';
import RowPeerMobile from '../components/organisms/RowPeerMobile.vue';
import TableRowPeer from '../components/organisms/TableRowPeer.vue';
import DialogSave from '../components/organisms/DialogSave.vue';
import { getServer } from '@/modules/server/services/fetch';
import type { AxiosErrorResponse } from '@/types';
import { useRouter } from 'vue-router';

const router = useRouter()

const peers = ref<Peer[]>([])
const showRowSave = ref(false)

const bootting = ref(false)
async function boot() {
    bootting.value = true
    try {
        const res = await getPeers();
        if (res.data.data.length > 0) {
            peers.value = res.data.data
            return;
        }
        await getServer()
            .catch((error) => {
                if (isAxiosError<AxiosErrorResponse>(error) && error.response) {
                    const { message } = error.response.data
                    if ('Server not found' === message) {
                        router.push('/server/new')
                    }
                } else {
                    console.error(error)
                }
            })
        peers.value = res.data.data
    } finally {
        bootting.value = false
    }
}

onMounted(() => boot())

async function handleUpdate(peer: Peer, index: number) {
    peers.value[index] = peer
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
            @delete="handleDelete(peer, index)" @update="handleUpdate(peer, $event)" />
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
                        @delete="handleDelete(peer, index)" @updated="handleUpdate($event, index)" />
                </TableBody>
                <TableFooter>
                    <RowSave @cancel="showRowSave = false" @save="handleSave" />
                </TableFooter>
            </Table>
        </div>
    </ContainerCenter>
</template>
