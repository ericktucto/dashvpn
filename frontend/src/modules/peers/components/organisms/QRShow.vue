<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { QrCodeIcon } from 'lucide-vue-next';
import { getConfigPeer, type Peer } from '../../services/fetch';
import QRCode from 'qrcode'
import { ref } from 'vue';

defineProps<{
    peer: Peer
}>()

defineEmits<{
    confirm: []
}>()

const dataUrl = ref('')

function handleClick(slug: string) {
    getConfigPeer(slug).then(async (res) => {
        dataUrl.value = await QRCode.toDataURL(res.data)
    })
}

</script>

<template>
    <Dialog>
        <form>
            <DialogTrigger as-child>
                <Button variant="outline" @click="handleClick(peer.slug)">
                    <QrCodeIcon />
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ peer.name }}</DialogTitle>
                </DialogHeader>
                <div class="grid justify-center">
                    <img :src="dataUrl" />
                </div>
            </DialogContent>
        </form>
    </Dialog>
</template>
