<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter, CardAction } from '@/components/ui/card';
import { type Peer } from '../../services/fetch';
import { reactive, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { BanIcon, EditIcon, SaveIcon } from 'lucide-vue-next';
import QRShow from './QRShow.vue';
import ConfirmDelete from './ConfirmDelete.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import ShareLink from './ShareLink.vue';
import DownloadPeerConf from '../../molecules/DownloadPeerConf.vue';
import { handleUpdate } from '../../composables/useUpdatePeer';

defineProps<{
    peer: Peer
}>()

const emit = defineEmits<{
    delete: []
    updated: [form: Peer]
}>()

const editing = ref(false)
const form = reactive({
    name: '',
    address: '',
})

function handleEdit(peer: Peer) {
    editing.value = true
    form.name = peer.name
    form.address = peer.address
}
async function onUpdatePeer(peer: Peer) {
    const res = await handleUpdate(peer, form)
    if (res !== null) {
        editing.value = false
        emit('updated', res)
    }
}
</script>
<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ peer.name }}</CardTitle>
            <CardDescription>{{ peer.address }}</CardDescription>
            <CardAction>
                <Switch :modelValue="true" />
            </CardAction>
        </CardHeader>
        <CardContent :class="[editing ? 'grid' : 'hidden']" class="space-y-4">
            <Label>Nombre</Label>
            <Input v-model="form.name" />
            <Label>Interface Address</Label>
            <Input v-model="form.address" />
        </CardContent>
        <CardFooter>
            <div :class="[editing ? 'flex' : 'hidden']" class="justify-between w-full gap-6">
                <Button variant="outline" @click="editing = false">
                    <BanIcon />
                    Cancelar
                </Button>
                <Button :disabled="!peer.name" @click="onUpdatePeer(peer)">
                    <SaveIcon />
                    Guardar
                </Button>
            </div>
            <div :class="[editing ? 'hidden' : 'inline-flex']" class="gap-2 justify-between w-full">
                <Button variant="secondary" @click="handleEdit(peer)">
                    <EditIcon />
                </Button>
                <DownloadPeerConf :peer="peer" />
                <ShareLink :peer="peer" />
                <QRShow :peer="peer" />
                <ConfirmDelete :peer="peer" @deleted="$emit('delete')" />
            </div>
        </CardFooter>
    </Card>
</template>
