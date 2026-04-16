<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { LinkIcon } from 'lucide-vue-next';
import { getLink, type Peer } from '../../services/fetch';
import { reactive } from 'vue';
import { toast } from 'vue-sonner';
import { isAxiosError } from 'axios';
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group';

defineProps<{
    peer: Peer
}>()

defineEmits<{
    confirm: []
}>()

const data = reactive({
    url: '',
    otp: '',
})

async function handleLink(peer: Peer) {
    try {
        const response = await getLink(peer.slug)
        data.url = response.data.data.url
        data.otp = response.data.data.otp
    } catch (error) {
        if (isAxiosError(error) && error.response) {
            toast.error(error.response.data.message)
        } else {
            console.log(error)
        }
    }
}

function handleCopy() {
    navigator.clipboard.writeText(data.url).then(() => {
        toast.success('Copiado')
    })
}

</script>

<template>
    <Dialog>
        <form>
            <DialogTrigger as-child>
                <Button variant="outline" @click="handleLink(peer)">
                    <LinkIcon />
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ peer.name }}</DialogTitle>
                    <DialogDescription>Link vence en 15 minutos</DialogDescription>
                </DialogHeader>
                <div class="flex">
                    <InputGroup>
                        <InputGroupInput v-model="data.url" readonly />
                        <InputGroupAddon align="inline-end">
                            <InputGroupButton @click="handleCopy" variant="secondary" class="hover:cursor-pointer">
                                Copiar
                            </InputGroupButton>
                        </InputGroupAddon>
                    </InputGroup>
                </div>
                <div class="flex justify-center text-center text-5xl my-8">
                    <h3>{{ data.otp }}</h3>
                </div>
                <DialogFooter>
                    <div class="grid w-full gap-4">
                        <Button variant="outline" class="w-full">
                            Copiar OTP
                        </Button>
                        <DialogClose as-child>
                            <Button class="w-full">
                                Cerrar
                            </Button>
                        </DialogClose>
                    </div>
                </DialogFooter>
            </DialogContent>
        </form>
    </Dialog>
</template>
