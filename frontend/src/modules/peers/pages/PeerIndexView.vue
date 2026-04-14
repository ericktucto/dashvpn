<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import ContainerCenter from '@/layouts/ContainerCenter.vue';
import { onMounted, ref } from 'vue';
import { deletePeer, getPeers, postPeer, type Peer } from '../services/fetch';
import { Button } from '@/components/ui/button';
import { DownloadIcon, EditIcon, PlusIcon, QrCodeIcon, Share2Icon } from 'lucide-vue-next';
import ConfirmDelete from '../components/organisms/ConfirmDelete.vue';
import { toast } from 'vue-sonner';
import RowSave from '../components/organisms/RowSave.vue';
import { isAxiosError } from 'axios';

const peers = ref<Peer[]>([])
const showRowSave = ref(false)

onMounted(() => {
    getPeers().then(res => peers.value = res.data.data)
})

function handleDelete(peer: Peer, index: number) {
    deletePeer(peer.slug).then(res => {
        toast.info(res.data.message)
        peers.value.splice(index, 1)
    })
}
function handleSave(name: string) {
    showRowSave.value = false
    postPeer(name).then(res => {
        peers.value.push(res.data.data)
    }).catch(error => {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
    })
}
</script>

<template>
    <ContainerCenter>
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
                    <TableRow v-for="(peer, index) in peers" :key="`${peer.slug}-row`">
                        <TableCell class="font-medium pl-8">
                            {{ peer.name }}
                        </TableCell>
                        <TableCell>{{ peer.address }}</TableCell>
                        <TableCell>activo</TableCell>
                        <TableCell class="text-right pr-8">
                            <div class="inline-flex gap-2">
                                <Button variant="secondary">
                                    <EditIcon />
                                </Button>
                                <Button variant="outline">
                                    <DownloadIcon />
                                </Button>
                                <Button variant="outline">
                                    <Share2Icon />
                                </Button>
                                <Button variant="outline">
                                    <QrCodeIcon />
                                </Button>
                                <ConfirmDelete :peer="peer" @confirm="handleDelete(peer, index)" />
                            </div>
                        </TableCell>
                    </TableRow>
                    <RowSave v-if="showRowSave" @cancel="showRowSave = false" @save="handleSave" />
                </TableBody>
                <TableFooter>
                    <TableRow>
                        <TableCell class="font-medium pl-8">
                        </TableCell>
                        <TableCell></TableCell>
                        <TableCell></TableCell>
                        <TableCell class="text-right pr-8">
                            <Button @click="showRowSave = true">
                                <PlusIcon />
                                Agregar Peer
                            </Button>
                        </TableCell>
                    </TableRow>
                </TableFooter>
            </Table>
        </div>
    </ContainerCenter>
</template>
