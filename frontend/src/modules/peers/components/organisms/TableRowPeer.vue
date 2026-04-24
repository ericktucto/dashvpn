<script setup lang="ts">
import { computed, reactive } from 'vue';
import type { Peer } from '../../services/fetch';
import DownloadPeerConf from '../../molecules/DownloadPeerConf.vue';
import { TableCell, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { BanIcon, EditIcon, SaveIcon } from 'lucide-vue-next';
import QRShow from './QRShow.vue';
import ConfirmDelete from './ConfirmDelete.vue';
import ShareLink from './ShareLink.vue';
import { handleUpdate } from '../../composables/useUpdatePeer';
import { useCrudStore } from '../../stores/crud';

const crudStore = useCrudStore()

const editing = computed(() => crudStore.updating == props.peer.slug)
const disabled = computed(() => {
    if (crudStore.adding) {
        return true
    }
    if (crudStore.updating && crudStore.updating != props.peer.slug) {
        return true
    }
    return false
})

const form = reactive({
    name: '',
    address: '',
})

const props = defineProps<{
    peer: Peer,
}>()

const emit = defineEmits<{
    delete: []
    updated: [form: Peer]
}>()
function handleEdit(peer: Peer) {
    crudStore.addProcess('updating', peer.slug)
    form.name = peer.name
    form.address = peer.address
}
async function onUpdatePeer(peer: Peer) {
    const res = await handleUpdate(peer, form)
    if (res !== null) {
        crudStore.removeProcess('updating')
        emit('updated', res)
    }
}
</script>
<template>
    <TableRow>
        <TableCell class="font-medium pl-8">
            <span v-show="!editing">{{ peer.name }}</span>
            <Input v-show="editing" v-model.trim="form.name" />
        </TableCell>
        <TableCell>
            <span v-show="!editing">{{ peer.address }}</span>
            <Input v-show="editing" v-model.trim="form.address" />
        </TableCell>
        <TableCell>
            <Switch :modelValue="true" />
        </TableCell>
        <TableCell class="text-right pr-8">
            <div :class="[editing ? 'flex' : 'hidden']" class="justify-end gap-6">
                <Button variant="outline" @click="crudStore.removeProcess('updating')">
                    <BanIcon />
                    Cancelar
                </Button>
                <Button :disabled="!peer.name" @click="onUpdatePeer(peer)">
                    <SaveIcon />
                    Actualizar
                </Button>
            </div>
            <div :class="[!editing ? 'inline-flex' : 'hidden']" class="gap-2">
                <Button variant="secondary" @click="handleEdit(peer)" :disabled="disabled">
                    <EditIcon />
                </Button>
                <DownloadPeerConf :peer="peer" />
                <ShareLink :peer="peer" />
                <QRShow :peer="peer" />
                <ConfirmDelete :peer="peer" @deleted="$emit('delete')" />
            </div>
        </TableCell>
    </TableRow>
</template>
