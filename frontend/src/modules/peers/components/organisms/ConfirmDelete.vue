<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { TrashIcon } from 'lucide-vue-next';
import { deletePeer, type Peer } from '../../services/fetch';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { useCrudStore } from '../../stores/crud';

const crudStore = useCrudStore()

const props = defineProps<{
    peer: Peer
}>()

const emit = defineEmits<{
    deleted: [peer: Peer]
}>()

const nombre = ref('')

function handleDelete(peer: Peer) {
    deletePeer(peer.slug).then(res => {
        toast.info(res.data.message)
        emit('deleted', peer)
    })
}

const disabled = computed(() => {
    if (crudStore.updating || crudStore.adding) {
        return true
    }
    return false
})
function handleClose() {
    crudStore.removeProcess("deleting")
}
</script>

<template>
    <Dialog>
        <form>
            <DialogTrigger as-child>
                <Button variant="destructive" @click="crudStore.addProcess('deleting', peer.slug)" :disabled="disabled">
                    <TrashIcon />
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]" @close-auto-focus="handleClose">
                <DialogHeader>
                    <DialogTitle>Eliminar Peer</DialogTitle>
                </DialogHeader>
                <div class="grid gap-4">
                    <p>Esta acción es permanente, para confirmar, escribe el nombre del peer <strong>({{ peer.name
                            }})</strong></p>
                    <Input name="name" placeholder="Escribe el nombre del peer" v-model="nombre" />
                </div>
                <DialogFooter>
                    <div class="flex justify-between w-full mt-4">
                        <DialogClose as-child>
                            <Button variant="outline">
                                Cancel
                            </Button>
                        </DialogClose>
                        <DialogClose as-child>
                            <Button type="submit" variant="destructive" :disabled="nombre != peer.name"
                                @click="handleDelete(peer)">
                                Delete
                            </Button>
                        </DialogClose>
                    </div>
                </DialogFooter>
            </DialogContent>
        </form>
    </Dialog>
</template>
