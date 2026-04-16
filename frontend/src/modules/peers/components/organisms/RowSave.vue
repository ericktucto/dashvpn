<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { TableCell, TableRow } from '@/components/ui/table';
import { BanIcon, PlusIcon, SaveIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import { getNextAddress, postPeer, type Peer } from '../../services/fetch';
import { isAxiosError } from 'axios';
import { toast } from 'vue-sonner';

const name = ref('')
const nextIp = ref('')
const show = ref(false)

const emit = defineEmits<{
    cancel: []
    save: [newPeer: Peer]
}>()

function handleShow() {
    show.value = true
    getNextAddress().then((res) => {
        nextIp.value = res.data.data.address
    })
}
function handleSave(name: string) {
    postPeer(name).then(res => {
        emit('save', res.data.data)
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
    <TableRow>
        <TableCell>
            <Input class="bg-white" v-show="show" v-model.trim="name" />
        </TableCell>
        <TableCell>
            <span v-show="show">{{ nextIp }}</span>
        </TableCell>
        <TableCell>
            <Switch v-show="show" :modelValue="true" />
        </TableCell>
        <TableCell class="text-right pr-8">
            <Button @click="handleShow" v-show="!show">
                <PlusIcon />
                Agregar Peer
            </Button>
            <div class="justify-end gap-6" :class="[show ? 'flex' : 'hidden']">
                <Button variant="outline" @click="show = false">
                    <BanIcon />
                    Cancelar
                </Button>
                <Button :disabled="!name" @click="handleSave(name)">
                    <SaveIcon />
                    Guardar
                </Button>
            </div>
        </TableCell>
    </TableRow>
</template>
