<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { onMounted } from 'vue';
import { ref } from 'vue';
import { getNextAddress, postPeer, type Peer } from '../../services/fetch';
import { PlusIcon } from 'lucide-vue-next';
import { Label } from '@/components/ui/label';
import { isAxiosError } from 'axios';
import { toast } from 'vue-sonner';

const name = ref('')
const nextIp = ref('')

const emit = defineEmits<{
    cancel: []
    save: [newPeer: Peer]
}>()

onMounted(() => {
    getNextAddress().then((res) => {
        nextIp.value = res.data.data.address
    })
})
function handleSave(name: string, close: () => void) {
    postPeer(name).then(res => {
        emit('save', res.data.data)
        close()
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
    <Dialog v-slot="{ close }">
        <form>
            <DialogTrigger as-child>
                <Button class="fixed bottom-4 right-4 size-12">
                    <PlusIcon class="size-6" />
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Nuevo Peer</DialogTitle>
                </DialogHeader>
                <div class="grid gap-4">
                    <Label>Nombre</Label>
                    <Input v-model="name" placeholder="Escribe el nombre del peer" />
                    <Label>Interface Address</Label>
                    <Input v-model="nextIp" readonly />
                </div>
                <DialogFooter>
                    <div class="flex justify-between w-full mt-4">
                        <Button variant="outline" @click="close()">
                            Cancel
                        </Button>
                        <Button variant="destructive" :disabled="!name" @click="handleSave(name, close)">
                            Guardar
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </form>
    </Dialog>
</template>
