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
import type { Peer } from '../../services/fetch';
import { ref } from 'vue';

defineProps<{
    peer: Peer
}>()

defineEmits<{
    confirm: []
}>()

const nombre = ref('')

</script>

<template>
    <Dialog>
        <form>
            <DialogTrigger as-child>
                <Button variant="destructive">
                    <TrashIcon />
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
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
                            <Button type="submit" :disabled="nombre != peer.name" @click="$emit('confirm')">
                                Save changes
                            </Button>
                        </DialogClose>
                    </div>
                </DialogFooter>
            </DialogContent>
        </form>
    </Dialog>
</template>
