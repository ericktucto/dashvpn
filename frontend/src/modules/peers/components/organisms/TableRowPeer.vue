<script setup lang="ts">
import { reactive, ref } from 'vue';
import type { Peer, PutPeer } from '../../services/fetch';
import DownloadPeerConf from '../../molecules/DownloadPeerConf.vue';
import { TableCell, TableRow } from '@/components/ui/table';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { BanIcon, EditIcon, SaveIcon } from 'lucide-vue-next';
import QRShow from './QRShow.vue';
import ConfirmDelete from './ConfirmDelete.vue';
import ShareLink from './ShareLink.vue';

const editing = ref(false)
const form = reactive({
    name: '',
    address: '',
})

defineProps<{
    peer: Peer,
}>()

defineEmits<{
    delete: []
    update: [form: PutPeer]
}>()
function handleEdit(peer: Peer) {
    editing.value = true
    form.name = peer.name
    form.address = peer.address
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
                <Button variant="outline" @click="editing = false">
                    <BanIcon />
                    Cancelar
                </Button>
                <Button :disabled="!peer.name" @click="$emit('update', form)">
                    <SaveIcon />
                    Actualizar
                </Button>
            </div>
            <div :class="[!editing ? 'inline-flex' : 'hidden']" class="gap-2">
                <Button variant="secondary" @click="handleEdit(peer)">
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
