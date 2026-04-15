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
import { onMounted, reactive, ref } from 'vue';
import { deletePeer, getConfigPeer, getPeers, postPeer, putPeer, type Peer, type PutPeer } from '../services/fetch';
import { Button } from '@/components/ui/button';
import { BanIcon, DownloadIcon, EditIcon, LinkIcon, PlusIcon, SaveIcon } from 'lucide-vue-next';
import ConfirmDelete from '../components/organisms/ConfirmDelete.vue';
import { toast } from 'vue-sonner';
import RowSave from '../components/organisms/RowSave.vue';
import { isAxiosError } from 'axios';
import QRShow from '../components/organisms/QRShow.vue';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import RowPeerMobile from '../components/organisms/RowPeerMobile.vue';

const peers = ref<Peer[]>([])
const showRowSave = ref(false)
const editing = ref(-1)
const form = reactive({
    name: '',
    address: '',
})

onMounted(() => {
    getPeers().then(res => peers.value = res.data.data)
})

async function handleDownload(peer: Peer) {
    const res = await getConfigPeer(peer.slug)
    const blob = new Blob([res.data], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)

    const a = document.createElement('a')
    a.href = url
    a.download = `${peer.slug}.conf`
    a.click()

    URL.revokeObjectURL(url)
}

async function handleUpdate(peer: Peer, newData: PutPeer, index: number) {
    const validIp = /^\d+\.\d+\.\d+\.\d+$/.test(newData.address)
    if (!validIp) {
        toast.error('Address invalid')
    }
    try {
        const response = await putPeer(peer.slug, newData)
        peers.value[index] = response.data.data
        toast.info(response.data.message)
        editing.value = -1
    } catch (error) {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
    }
}
function handleEdit(peer: Peer, index: number) {
    editing.value = index
    form.name = peer.name
    form.address = peer.address
}
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
    <div class="w-screen mt-[69px] p-8 grid md:hidden gap-4">
        <RowPeerMobile v-for="(peer, index) in peers" :key="`${peer.slug}-rowpeermobile`" :peer="peer"
            @download="handleDownload(peer)" @delete="handleDelete(peer, index)"
            @update="handleUpdate(peer, $event, index)" />
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
                    <TableRow v-for="(peer, index) in peers" :key="`${peer.slug}-row`">
                        <TableCell class="font-medium pl-8">
                            <span v-show="editing !== index">{{ peer.name }}</span>
                            <Input v-show="editing == index" v-model.trim="form.name" />
                        </TableCell>
                        <TableCell>
                            <span v-show="editing !== index">{{ peer.address }}</span>
                            <Input v-show="editing == index" v-model.trim="form.address" />
                        </TableCell>
                        <TableCell>
                            <Switch :modelValue="true" />
                        </TableCell>
                        <TableCell class="text-right pr-8">
                            <div :class="[editing === index ? 'flex' : 'hidden']" class="justify-end gap-6">
                                <Button variant="outline" @click="editing = -1">
                                    <BanIcon />
                                    Cancelar
                                </Button>
                                <Button :disabled="!peer.name" @click="handleUpdate(peer, form, index)">
                                    <SaveIcon />
                                    Guardar
                                </Button>
                            </div>
                            <div :class="[editing !== index ? 'inline-flex' : 'hidden']" class="gap-2">
                                <Button variant="secondary" @click="handleEdit(peer, index)">
                                    <EditIcon />
                                </Button>
                                <Button variant="outline" @click="handleDownload(peer)">
                                    <DownloadIcon />
                                </Button>
                                <Button variant="outline">
                                    <LinkIcon />
                                </Button>
                                <QRShow :peer="peer" />
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
