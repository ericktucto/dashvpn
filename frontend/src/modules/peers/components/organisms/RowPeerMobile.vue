<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter, CardAction } from '@/components/ui/card';
import { type Peer, type PutPeer } from '../../services/fetch';
import { reactive, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { DownloadIcon, EditIcon, LinkIcon } from 'lucide-vue-next';
import QRShow from './QRShow.vue';
import ConfirmDelete from './ConfirmDelete.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

defineProps<{
    peer: Peer
}>()

defineEmits<{
    delete: []
    download: []
    update: [form: PutPeer]
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
                <Button :disabled="!peer.name" @click="$emit('update', form)">
                    <SaveIcon />
                    Guardar
                </Button>
            </div>
            <div :class="[editing ? 'hidden' : 'inline-flex']" class="gap-2 justify-between w-full">
                <Button variant="secondary" @click="handleEdit(peer)">
                    <EditIcon />
                </Button>
                <Button variant="outline" @click="$emit('download')">
                    <DownloadIcon />
                </Button>
                <Button variant="outline">
                    <LinkIcon />
                </Button>
                <QRShow :peer="peer" />
                <ConfirmDelete :peer="peer" @confirm="$emit('delete')" />
            </div>
        </CardFooter>
    </Card>
</template>
