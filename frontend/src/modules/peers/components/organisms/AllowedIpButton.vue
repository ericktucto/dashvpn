<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    DialogDescription,
} from '@/components/ui/dialog'
import { ShieldCheck } from 'lucide-vue-next';
import { ref } from 'vue';
import InstructionsStack from '@/components/dashvpn/InstructionsStack.vue';

const props = withDefaults(defineProps<{
    allowed: Array<string>
    readOnly?: boolean
}>(), {
    readOnly: false
})

const emit = defineEmits<{
    "update:allowed": [payload: Array<string>]
}>()

const ips = ref<string[]>([])

function handleOpen() {
    ips.value = props.allowed
}
function handleSave(close: () => void) {
    emit('update:allowed', ips.value)
    close()
}
</script>

<template>
    <Dialog v-slot="{ close }">
        <form>
            <DialogTrigger as-child>
                <Button variant="outline" @click="handleOpen">
                    <ShieldCheck /> Allowed Ips
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Ips permitidas</DialogTitle>
                    <DialogDescription></DialogDescription>
                </DialogHeader>
                <div class="grid justify-center">
                    <InstructionsStack v-model:instructions="ips" placeholder="Escribe un ip permitada"
                        :readOnly="readOnly" />
                </div>
                <DialogFooter v-show="!readOnly">
                    <div class="flex justify-between w-full mt-4">
                        <Button variant="outline" @click="close()">
                            Cancel
                        </Button>
                        <Button @click="handleSave(close)">
                            Guardar
                        </Button>
                    </div>
                </DialogFooter>
            </DialogContent>
        </form>
    </Dialog>
</template>
